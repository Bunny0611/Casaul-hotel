# Fix Plan - 500 Error & Code Improvements

## Issues Identified
1. **index.blade.php - Malformed HTML** - Extra `</div>` and `</section>` tags between sections causing 500 error
2. **section-hero.blade.php - Extra trailing `</section>`** tag
3. **app.blade.php - JS loaded in `<head>`** instead of bottom of `<body>`

## Steps
- [x] Create TODO.md
- [x] Fix `index.blade.php` - Removed extraneous `</div>` and `</section>` tags between sections
- [x] Fix `section-hero.blade.php` - Removed trailing `</section>` tag
- [x] Fix `app.blade.php` - Moved JS script tag from `<head>` to bottom of `<body>`
