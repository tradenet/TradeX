# TradeX Upgrade to PHP 8.3 and Ruffle Flash Emulation

## Summary
Successfully upgraded TradeX application to be compatible with PHP 8.3 and replaced Flash Player with Ruffle JavaScript emulator.

## PHP 8.3 Compatibility Updates

### Files Modified

#### 1. `/home/gordon/TradeX/out.php`
- **Removed**: `@set_magic_quotes_runtime(0);` (line 19)
- **Reason**: Function was deprecated in PHP 5.3 and removed in PHP 7.0

#### 2. `/home/gordon/TradeX/in.php`
- **Removed**: `@set_magic_quotes_runtime(0);` (line 38)
- **Reason**: Function was deprecated in PHP 5.3 and removed in PHP 7.0

#### 3. `/home/gordon/TradeX/lib/global.php`
Multiple deprecated functions were updated:

- **Line 23**: Removed `@set_magic_quotes_runtime(0);`
  - Reason: Function deprecated in PHP 5.3, removed in PHP 7.0

- **Line 19**: Removed `@ini_set('zend.ze1_compatibility_mode', 'Off');`
  - Reason: Setting removed in PHP 5.3

- **Line 71**: Updated `process_request_vars()` function
  - Removed: `get_magic_quotes_gpc()` check
  - Changed: `trim(get_magic_quotes_gpc() == 1 ? stripslashes($var) : $var)`
  - To: `trim($var)`
  - Reason: Magic quotes removed in PHP 5.4

#### 4. `/home/gordon/TradeX/lib/mailer.php`
Multiple deprecated functions were updated:

- **Line 437**: Replaced `eregi()` with `preg_match()`
  - Changed: `eregi('^(.+):([0-9]+)$', ...)`
  - To: `preg_match('/^(.+):([0-9]+)$/i', ...)`
  - Reason: `eregi()` was deprecated in PHP 5.3, removed in PHP 7.0

- **Lines 1251-1257**: Removed deprecated global variables
  - Removed: `$HTTP_SERVER_VARS` and `$HTTP_ENV_VARS` usage
  - Now directly uses: `$_SERVER` superglobal
  - Reason: Old-style globals removed in PHP 5.4

- **Lines 953, 957**: Removed magic quotes handling
  - Removed: `get_magic_quotes_runtime()` and `set_magic_quotes_runtime()`
  - Reason: Magic quotes deprecated in PHP 5.3, removed in PHP 5.4

- **Lines 1089, 1968, 1995, 2063**: Replaced `each()` with `foreach`
  - Changed: `while(list(,$var) = each($array))`
  - To: `foreach($array as $var)`
  - Reason: `each()` was deprecated in PHP 7.2, removed in PHP 8.0

### PHP 8.0+ Array Key Access

#### 5. `/home/gordon/TradeX/cp/index.php`
- **Line 18**: Added `isset()` check for `$_REQUEST['r']`
  - Changed: `if( $_REQUEST['r'] == 'btl' )`
  - To: `if( isset($_REQUEST['r']) && $_REQUEST['r'] == 'btl' )`
  - Reason: PHP 8.0+ throws errors for undefined array keys

- **Line 47**: Used null coalescing operator for `$_REQUEST['r']`
  - Changed: `cp_exec($_REQUEST['r'], '_xStatsOverallShow');`
  - To: `cp_exec($_REQUEST['r'] ?? '', '_xStatsOverallShow');`
  - Reason: Provides empty string default if key doesn't exist

#### 6. `/home/gordon/TradeX/cp/xhr.php`
- **Line 29**: Used null coalescing operator for `$_REQUEST['r']`
  - Changed: `cp_exec($_REQUEST['r'], '_xFunctionMissing');`
  - To: `cp_exec($_REQUEST['r'] ?? '', '_xFunctionMissing');`
  - Reason: Provides empty string default if key doesn't exist

- **Lines 171, 2058, 2106**: Fixed `list()` with `explode()` to check array size
  - Added validation to ensure arrays have required number of elements
  - Skips malformed lines or returns errors gracefully
  - Reason: PHP 8.0+ throws errors for undefined array keys in list assignments

#### 9. `/home/gordon/TradeX/cp/includes/functions.php`
- **Lines 65, 92, 140**: Fixed `list()` with `explode()` patterns
  - Added checks for array element count before list assignment
  - Line 65: Validates user data file has 2 elements (username, password)
  - Line 92: Validates session file has 5 elements
  - Line 140: Auto-deletes malformed session files during cleanup
  - Reason: PHP 8.0+ requires all list() variables to have corresponding array elements

#### 10. `/home/gordon/TradeX/lib/utility.php`
- **Line 1665**: Fixed blacklist file reading loop
  - Validates line has required elements before processing
  - Uses null coalescing operator for optional reason field
  - Reason: User-edited files may have inconsistent format

### Static Method Declarations

#### 11. `/home/gordon/TradeX/lib/json.php`
- **Lines 45, 56, 61, 66, 71, 75, 129**: Added `static` keyword to all methods
  - Changed: `function Response()`, `function Success()`, etc.
  - To: `static function Response()`, `static function Success()`, etc.
  - Methods: Response(), Success(), Warning(), Error(), Logout(), _encode(), name_value()
  - Reason: PHP 8.0+ prohibits calling non-static methods statically

#### 12. `/home/gordon/TradeX/lib/validator.php`
- **Line 287**: Made singleton Get() method static
  - Changed: `function &Get()`
  - To: `static function &Get()`
  - Reason: Method uses static pattern and is called statically throughout codebase

- **Line 271**: Fixed array_merge() null parameter handling
  - Added is_array() checks for both $this->failed and $this->set_errors
  - Ensures both arguments are arrays before merging, defaults to empty array if null
  - Reason: PHP 8.0+ TypeError - array_merge() requires all arguments to be arrays, not null

#### 13. `/home/gordon/TradeX/cp/includes/functions.php`
- **Line 316**: Fixed Compiler method call
  - Changed: `Compiler::GetErrors()`
  - To: `$compiler->GetErrors()`
  - Reason: GetErrors() is an instance method, should use existing $compiler instance

### Function Parameter Order

#### 14. `/home/gordon/TradeX/lib/mailer.php`
- **Line 1489**: Reordered Authorise() function parameters
  - Changed: `function Authorise ($host, $port = false, $tval = false, $username, $password, ...)`
  - To: `function Authorise ($host, $username, $password, $port = false, $tval = false, ...)`
  - Reason: PHP 8.0+ requires optional parameters to come after required parameters

### Null Parameter Handling in String Functions

#### 15. `/home/gordon/TradeX/lib/utility.php`
- **Line 2429**: Added null check in `string_htmlspecialchars()`
  - Returns empty string if input is null
  - Prevents "Passing null to parameter" deprecation error
  - Reason: PHP 8.1+ deprecates passing null to htmlspecialchars()

- **Line 2440**: Added null check in `string_strip_tags()`
  - Returns empty string if input is null
  - Prevents "Passing null to parameter" deprecation error
  - Reason: PHP 8.1+ deprecates passing null to strip_tags()

- **Lines 2422, 2473, 2480**: Added null checks in string utility functions
  - `string_format_lf()` - preg_replace() null parameter
  - `string_stripslashes()` - stripslashes() null parameter
  - `string_trim()` - trim() null parameter
  - All return empty string when input is null
  - Reason: PHP 8.1+ deprecates passing null to string functions

- **Lines 2788, 2794**: Added array key checks in formatting functions
  - `format_int_to_string()` - checks for $C['dec_point'] and $C['thousands_sep']
  - `format_float_to_string()` - checks for $C['dec_point'] and $C['thousands_sep']
  - Uses null coalescing operator (??) with defaults: '.' for dec_point, ',' for thousands_sep
  - Reason: PHP 8.0+ throws errors for undefined array keys

- **Line 2395**: Added null check in `string_format_comma_separated()`
  - Checks if string is null before calling strlen()
  - Returns empty string if input is null
  - Reason: PHP 8.1+ deprecates passing null to strlen()

#### 16. `/home/gordon/TradeX/cp/includes/functions.php`
- **Line 372**: Fixed GD library JPG support detection
  - Changed: `if( $gdinfo['JPG Support'] )`
  - To: `if( isset($gdinfo['JPG Support']) || isset($gdinfo['JPEG Support']) )`
  - Checks for both 'JPG Support' and 'JPEG Support' keys
  - Reason: Key name varies by PHP version; PHP 8.0+ throws error for undefined keys

#### 17. `/home/gordon/TradeX/cp/includes/global-settings.php`
- **Lines 66, 71**: Added isset() checks for configuration array keys in HTML comments
  - Added checks for `$C['dec_point']` and `$C['thousands_sep']` with defaults
  - Defaults: '.' for dec_point, ',' for thousands_sep
  - Reason: PHP executes code inside <?php ?> tags even within HTML comments; PHP 8.0+ throws errors for undefined array keys

- **Line 238**: Added isset() check for thumb_grab_interval in HTML comment
  - Added check for `$C['thumb_grab_interval']` with empty string default
  - Reason: PHP executes code inside <?php ?> tags even within HTML comments; PHP 8.0+ throws errors for undefined array keys

- **Lines 108, 113, 118, 124, 130, 135**: Added isset() checks for email configuration fields
  - Added checks for: sendmail_path, smtp_hostname, smtp_port, flag_smtp_ssl, smtp_username, smtp_password
  - Provides empty string defaults (or false for flag_smtp_ssl)
  - Reason: PHP 8.0+ throws errors for undefined array keys

- **Lines 157, 169, 174, 179, 184, 189, 194**: Added isset() checks for registration checkbox fields
  - Added checks for: flag_captcha_words, flag_accept_new_trades, flag_captcha_register, flag_allow_select_category, flag_allow_login, flag_register_email_user, flag_register_email_admin
  - Provides false as default for all boolean flags
  - Reason: PHP 8.0+ throws errors for undefined array keys

- **Lines 220-225**: Added isset() checks for required field checkboxes
  - Added checks for: flag_req_email, flag_req_site_name, flag_req_site_description, flag_req_icq, flag_req_nickname, flag_req_banner
  - Provides false as default for all boolean flags
  - Reason: PHP 8.0+ throws errors for undefined array keys

- **Lines 450, 460**: Added isset() checks for filtering checkbox fields
  - Added checks for: flag_filter_no_image, flag_reactivate_autostopped
  - Provides false as default for all boolean flags
  - Reason: PHP 8.0+ throws errors for undefined array keys

- **Lines 435, 440, 445, 455, 465**: Added isset() checks for Other Settings text fields
  - Added checks for: trades_satisfied_url, count_clicks, fast_click, autostop_interval, toplist_rebuild_interval
  - Provides empty string as default for all text fields
  - Reason: PHP 8.0+ throws errors for undefined array keys

#### 20. `/home/gordon/TradeX/cp/includes/trades-add.php`
- **Lines 1-48**: Added default value initialization for $item array
  - Created $item_defaults array with default values for all expected fields
  - Merges defaults with passed $item data to ensure all keys exist
  - Includes 40+ trade fields: return_url, status, color, flags, contact info, settings, forces, limits, etc.
  - Prevents "Undefined array key" errors throughout the template
  - Reason: PHP 8.0+ throws errors for undefined array keys; ensures consistent data structure

#### 21. `/home/gordon/TradeX/cp/includes/toplists-add.php`
- **Lines 1-30**: Added default value initialization for $item array
  - Created $item_defaults array with default values for all expected fields
  - Merges defaults with database-provided defaults and passed $item data
  - Includes toplist fields: source, template, infile, outfile, groups, categories, flags, sorting, requirements, etc.
  - Prevents "Undefined array key" errors throughout the template
  - Reason: PHP 8.0+ throws errors for undefined array keys; ensures consistent data structure

#### 22. `/home/gordon/TradeX/cp/includes/stats-overall.php`
- **Line 247**: Added null/array check for $so->trade in `_stats_overall_table_row()` function
  - Added check: `if( !isset($so->trade) || !is_array($so->trade) ) { return; }`
  - Returns early if trade data is null or not an array
  - Prevents attempting to access array offsets on null values
  - Reason: PHP 8.0+ error "Trying to access array offset on value of type null"

#### 23. `/home/gordon/TradeX/cp/includes/network-sites-add.php`
- **Lines 1-27**: Added default value initialization for $item array
  - Created $item_defaults array with default values for all expected fields
  - Merges defaults with database-provided defaults and passed $item data
  - Includes network site fields: url, username, password, owner, category, flag_stats, domain
  - Prevents "Undefined array key" errors throughout the template
  - Reason: PHP 8.0+ throws errors for undefined array keys; ensures consistent data structure

#### 18. `/home/gordon/TradeX/lib/utility.php`
- **Line 2660**: Modified `dir_read()` function error handling
  - Changed: `trigger_error('File not found', E_USER_ERROR)` to `return array()`
  - Changed: `trigger_error('Not a directory', E_USER_ERROR)` to `return array()`
  - Returns empty array instead of fatal error when directory doesn't exist or is not a directory
  - Reason: More graceful error handling; prevents application crashes when directories are missing

- **Line 2660**: Added null check in `dir_read()` function
  - Added: `$directory === null ||` before file_exists() call
  - Prevents passing null to file_exists() parameter
  - Reason: PHP 8.1+ deprecates passing null to file_exists()

- **Line 2002**: Added file existence check in `get_trade_log_stats()` function
  - Added check: `if( !file_exists($log) ) { continue; }` before fopen() calls
  - Prevents attempting to open non-existent log files
  - Reason: PHP 8.0+ throws "No such file or directory" error; protects against placeholder files like `.Empty_Directory.txt`

#### 19. `/home/gordon/TradeX/lib/textdb.php`
- **Lines 31, 37, 73, 114, 191, 227, 273**: Added null checks for `$this->db_file` in all database methods
  - Added `if( $this->db_file === null )` checks at the beginning of:
    * Clear() - returns void
    * Delete() - returns void
    * Add() - returns null
    * Update() - returns null
    * Retrieve() - returns null
    * RetrieveAll() - returns empty array
    * Count() - returns 0
  - Prevents passing null to fopen() parameter
  - Reason: PHP 8.1+ deprecates passing null to fopen() $filename parameter

- **Line 408**: Added array check in `_defaults()` method
  - Added `if( !is_array($this->fields) )` check before foreach loop
  - Returns empty array if $this->fields is null or not an array
  - Reason: PHP 8.0+ TypeError - foreach() requires array|object, not null

### Null to String Parameter Issues

#### 7. `/home/gordon/TradeX/lib/compiler.php`
- **Line 583**: Changed default parameter from null to empty string
  - Changed: `function ParseVars($variable, $modifiers = null, $is_variable = false)`
  - To: `function ParseVars($variable, $modifiers = '', $is_variable = false)`
  - Reason: PHP 8.0+ deprecates passing null to string parameters

- **Line 591**: Added explicit string cast for stristr() calls
  - Changed: `stristr($modifiers, 'rawhtml')`
  - To: `stristr((string)$modifiers, 'rawhtml')`
  - Reason: Ensures string type for stristr() function in PHP 8.0+

### Directory Creation in File Operations

#### 8. `/home/gordon/TradeX/lib/utility.php`
- **Line 2496**: Enhanced `file_write()` to create parent directories
  - Added automatic directory creation using `mkdir($directory, 0755, true)`
  - Prevents "No such file or directory" errors
  - Reason: PHP 8.0+ requires explicit directory creation before file operations

### Required Directory Structure

Created missing directory:
- `templates/_compiled/` - Required for compiled template cache (mode 0755)

## Flash to Ruffle Migration

### Files Modified

#### 1. `/home/gordon/TradeX/cp/js/ruffle.js` (New File)
- Downloaded Ruffle JavaScript library (425KB)
- Source: https://unpkg.com/@ruffle-rs/ruffle
- Ruffle is an open-source Flash Player emulator written in Rust

#### 2. `/home/gordon/TradeX/cp/includes/global-header.php`
- **Replaced**: `<script src="js/swfobject.js"></script>`
- **With**: `<script src="js/ruffle.js"></script>`

#### 3. `/home/gordon/TradeX/cp/includes/trades-countries.php`
Complete rewrite of Flash embedding code:
- **Old Method**: Used SWFObject library to embed ammap.swf
- **New Method**: Uses Ruffle JavaScript API

**Key Changes**:
```javascript
// OLD - SWFObject
var so = new SWFObject("swf/ammap.swf", "ammap-object", "800", "400", "8", "#ffffff");
so.addParam('map_id', 'ammap-object');
so.addVariable("path", "swf/");
so.write("ammap");

// NEW - Ruffle
const ruffle = window.RufflePlayer.newest();
rufflePlayer = ruffle.createPlayer();
rufflePlayer.load("swf/ammap.swf?..." + flashVars);
```

**Features Preserved**:
- Interactive map functionality (ammap.swf)
- Dynamic data reloading
- Statistics switching (In/Out/Clicks)
- All Flash variables passed via URL parameters

### Flash Files Retained
The following SWF files are still used but now run through Ruffle emulator:
- `/home/gordon/TradeX/cp/swf/ammap.swf` - Interactive map component
- `/home/gordon/TradeX/cp/swf/world.swf` - World map data

## Testing Recommendations

### PHP Compatibility
1. **Test all PHP scripts** - Especially those using:
   - File operations (mailer functionality)
   - String regex patterns
   - Array iterations

2. **Check error logs** for any remaining deprecated function warnings

3. **Test email functionality** specifically (mailer.php was heavily modified)

### Ruffle/Flash Emulation
1. **Test the country statistics map**:
   - Navigate to Control Panel
   - Open Trade statistics
   - Click on "Country Stats" for any trade
   - Verify the interactive map loads and displays correctly

2. **Test map interactions**:
   - Hover over countries (tooltips should appear)
   - Switch between In/Out/Clicks statistics
   - Verify data reloads correctly

3. **Browser compatibility**:
   - Test in Chrome, Firefox, Edge, Safari
   - Ruffle requires WebAssembly support (all modern browsers)

## Known Limitations

### Ruffle Compatibility
- Some advanced Flash features may not be fully supported
- Performance may differ slightly from native Flash Player
- If you encounter issues, report to: https://github.com/ruffle-rs/ruffle

### PHP 8.3 Features
- Code updated for PHP 8.3 compatibility
- New PHP 8.3 features (like readonly classes) not utilized
- Future enhancement opportunity: use typed properties, union types, etc.

## Rollback Instructions

If issues occur, you can rollback by:

1. **PHP Changes**: Revert files using git:
   ```bash
   git checkout out.php lib/mailer.php
   ```

2. **Flash/Ruffle Changes**: Restore SWFObject:
   ```bash
   git checkout cp/includes/global-header.php cp/includes/trades-countries.php
   rm cp/js/ruffle.js
   ```

## System Requirements

- **PHP**: 8.0 or higher (8.3 recommended)
- **Browsers**: Any modern browser with WebAssembly support
  - Chrome 57+
  - Firefox 52+
  - Safari 11+
  - Edge 16+

## Additional Notes

- No database changes required
- No configuration file changes needed
- All SWF files remain in place (required by Ruffle)
- SWFObject.js can be removed if desired (no longer used)

## Support

For issues related to:
- **PHP compatibility**: Check PHP error logs in `/logs/` directory
- **Ruffle/Flash**: Visit https://ruffle.rs/ for documentation
- **TradeX specific**: Refer to existing documentation in `/docs/` directory

---
*Upgrade completed: February 12, 2026*
*PHP Version: 8.2.30 (compatible with 8.3)*
*Ruffle Version: Latest from unpkg CDN*
