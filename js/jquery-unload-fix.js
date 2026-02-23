/**
 * jQuery Unload Event Deprecation Fix
 * 
 * Modern browsers are deprecating the 'unload' event in favor of more reliable
 * alternatives. This patch intercepts jQuery's unload usage and replaces it
 * with modern event handlers (pagehide, visibilitychange).
 * 
 * Must be loaded after jQuery but before other scripts that might use unload.
 */
(function() {
    'use strict';
    
    if (!window.jQuery) {
        console.warn('jQuery not loaded - unload fix skipped');
        return;
    }
    
    var $ = window.jQuery;
    
    // Store original bind/unbind methods
    var originalBind = $.fn.bind;
    var originalUnbind = $.fn.unbind;
    var originalOne = $.fn.one;
    
    // Map of unload handlers to track them
    var unloadHandlers = [];
    
    /**
     * Execute all registered unload handlers
     */
    function executeUnloadHandlers(event) {
        for (var i = 0; i < unloadHandlers.length; i++) {
            try {
                unloadHandlers[i].call(this, event);
            } catch (e) {
                console.error('Error in unload handler:', e);
            }
        }
    }
    
    /**
     * Override jQuery's bind method to intercept 'unload' events
     */
    $.fn.bind = function(type, data, fn) {
        // If binding to 'unload', use modern alternatives instead
        if (type === 'unload' && this[0] === window) {
            var handler = fn || data;
            
            // Store the handler
            unloadHandlers.push(handler);
            
            // Use pagehide as primary (works in modern browsers)
            if ('onpagehide' in window) {
                originalBind.call(this, 'pagehide', data, fn);
            }
            
            // Use visibilitychange as fallback
            if (document.addEventListener) {
                document.addEventListener('visibilitychange', function(e) {
                    if (document.visibilityState === 'hidden') {
                        handler.call(window, e);
                    }
                }, false);
            }
            
            // Still use beforeunload for older browsers (doesn't trigger the warning)
            originalBind.call(this, 'beforeunload', data, fn);
            
            return this;
        }
        
        // For all other events, use original method
        return originalBind.call(this, type, data, fn);
    };
    
    /**
     * Override jQuery's unbind method to handle 'unload' events
     */
    $.fn.unbind = function(type, fn) {
        if (type === 'unload' && this[0] === window) {
            // Remove from our handlers list
            var handler = fn;
            for (var i = unloadHandlers.length - 1; i >= 0; i--) {
                if (unloadHandlers[i] === handler) {
                    unloadHandlers.splice(i, 1);
                }
            }
            
            // Unbind from modern events
            if ('onpagehide' in window) {
                originalUnbind.call(this, 'pagehide', fn);
            }
            originalUnbind.call(this, 'beforeunload', fn);
            
            return this;
        }
        
        return originalUnbind.call(this, type, fn);
    };
    
    /**
     * Override jQuery's one method to handle 'unload' events
     */
    $.fn.one = function(type, data, fn) {
        if (type === 'unload' && this[0] === window) {
            var handler = fn || data;
            var self = this;
            var oneTimeHandler = function(event) {
                handler.call(window, event);
                self.unbind('unload', oneTimeHandler);
            };
            return this.bind('unload', oneTimeHandler);
        }
        
        return originalOne.call(this, type, data, fn);
    };
    
    // Intercept any existing window.onunload assignments
    var originalOnUnload = window.onunload;
    if (originalOnUnload) {
        unloadHandlers.push(originalOnUnload);
    }
    
    // Override window.onunload setter
    Object.defineProperty(window, 'onunload', {
        get: function() {
            return function(e) {
                executeUnloadHandlers.call(window, e);
            };
        },
        set: function(handler) {
            if (handler && typeof handler === 'function') {
                unloadHandlers.push(handler);
            }
        },
        configurable: true
    });
    
    // Set up the modern event listeners
    if ('onpagehide' in window) {
        window.addEventListener('pagehide', executeUnloadHandlers, false);
    }
    
    if (document.addEventListener) {
        document.addEventListener('visibilitychange', function(e) {
            if (document.visibilityState === 'hidden') {
                executeUnloadHandlers.call(window, e);
            }
        }, false);
    }
    
    // Use beforeunload as a last resort (doesn't trigger deprecation warning)
    window.addEventListener('beforeunload', executeUnloadHandlers, false);
})();
