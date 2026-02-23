/**
 * JSON Protection against SES Lockdown
 * 
 * This script preserves native JSON functionality before browser extensions
 * (like MetaMask) apply SES lockdown which removes JSON.stringify/parse.
 * Must be loaded before any other scripts.
 */
(function() {
    'use strict';
    
    // Immediately capture the native JSON object
    var nativeJSON = window.JSON;
    var nativeStringify = nativeJSON.stringify.bind(nativeJSON);
    var nativeParse = nativeJSON.parse.bind(nativeJSON);
    
    // Create a protected JSON object that can't be modified
    var protectedJSON = {
        stringify: nativeStringify,
        parse: nativeParse,
        // Include other JSON methods that might be needed
        toString: function() { return '[object JSON]'; }
    };
    
    // Make the protected object immutable
    Object.freeze(protectedJSON);
    
    // Test if JSON works
    function testJSON() {
        try {
            if (window.JSON && typeof window.JSON.stringify === 'function') {
                window.JSON.stringify({test: 1});
                return true;
            }
            return false;
        } catch (e) {
            return false;
        }
    }
    
    // Aggressively restore JSON
    function forceRestore() {
        if (!testJSON()) {
            try {
                // Try to delete the broken property first
                delete window.JSON;
            } catch (e) {
                // Ignore errors
            }
            
            // Force set the protected JSON
            try {
                Object.defineProperty(window, 'JSON', {
                    value: protectedJSON,
                    writable: false,
                    configurable: false,
                    enumerable: false
                });
            } catch (e) {
                // If that fails, just set it normally
                window.JSON = protectedJSON;
            }
        }
    }
    
    // Run immediately
    forceRestore();
    
    // Run multiple times to catch extensions loading at different times
    var checkIntervals = [0, 10, 50, 100, 200, 500, 1000, 2000];
    checkIntervals.forEach(function(delay) {
        setTimeout(forceRestore, delay);
    });
    
    // Also set up a continuous monitor for the first few seconds
    var monitorCount = 0;
    var monitorInterval = setInterval(function() {
        forceRestore();
        monitorCount++;
        if (monitorCount > 20) { // Stop after ~2 seconds (20 * 100ms)
            clearInterval(monitorInterval);
        }
    }, 100);
    
    // Expose safe backup
    window.safeJSON = protectedJSON;
})();
