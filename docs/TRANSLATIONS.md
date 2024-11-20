# MMP Photo Minder Pro Translation Guide

Thank you for your interest in translating MMP Photo Minder Pro! This document will guide you through the process of creating and submitting translations for the plugin.

## Table of Contents
1. [Getting Started](#getting-started)
2. [Translation Process](#translation-process)
3. [Translation Guidelines](#translation-guidelines)
4. [RTL Language Support](#rtl-language-support)
5. [Common Translation Pitfalls](#common-translation-pitfalls)
6. [Testing Checklist](#testing-checklist)
7. [Technical Details](#technical-details)
8. [WordPress Core Consistency](#wordpress-core-consistency)
9. [Submitting Translations](#submitting-translations)
10. [Updating Translations](#updating-translations)
11. [Support](#support)
12. [Recognition](#recognition)
13. [Locale Codes Reference](#locale-codes-reference)

## Getting Started

### Prerequisites

1. A text editor that supports UTF-8 encoding (we recommend [Poedit](https://poedit.net/) for translation work)
2. Basic understanding of your target language's formal/informal tones
3. Familiarity with WordPress terminology in your language
4. For RTL languages: understanding of bidirectional text handling

### Available Files

In the `languages/` directory, you'll find:
- `mmp-photo.pot`: The template file containing all translatable strings
- `mmp-photo-{locale}.po`: Translation files for specific languages
- `mmp-photo-{locale}.mo`: Compiled translation files

Where `{locale}` follows the WordPress locale format: `language_COUNTRY` (e.g., `fr_FR` for French, `de_DE` for German).

## Translation Process

### Using Poedit (Recommended)

1. Download and install [Poedit](https://poedit.net/)
2. Create a new translation:
   - Open Poedit
   - Click "Create new translation"
   - Select the `mmp-photo.pot` file
   - Choose your target language
3. Translate each string:
   - The source text appears in the top panel
   - Enter your translation in the bottom panel
   - Use the context panel to understand where strings appear
4. Save your work:
   - File → Save as...
   - Use the naming format: `mmp-photo-{locale}.po`
   - Poedit will automatically generate the `.mo` file

### Manual Process

1. Copy `mmp-photo.pot` to `mmp-photo-{locale}.po`
2. Edit the `.po` file header:
   ```
   "Language-Team: YOUR LANGUAGE NAME <your@email.com>\n"
   "Language: xx_XX\n"
   ```
3. Translate each `msgstr` entry:
   ```
   msgid "Add New Gallery"
   msgstr "Your translation here"
   ```
4. Generate the `.mo` file using msgfmt:
   ```bash
   msgfmt -o mmp-photo-{locale}.mo mmp-photo-{locale}.po
   ```

## Translation Guidelines

1. **Maintain Consistency**
   - Use official WordPress term translations for your language
   - Keep formatting and punctuation consistent with the source text
   - Preserve placeholders (`%s`, `%d`, etc.) in their original position

2. **Context Matters**
   - Check the source file references for context
   - Consider the user interface location of each string
   - Pay attention to singular/plural forms

3. **Special Elements**
   - HTML tags: Must be preserved exactly (`<strong>`, `<a>`, etc.)
   - Variables: Preserve case sensitivity
   - Quotation marks: Use appropriate quotation style for your language

## RTL Language Support

### Setting Up RTL Support

1. **File Naming**
   - Create an RTL stylesheet: `gallery-rtl.css`
   - Include RTL-specific JavaScript adjustments

2. **CSS Considerations**
   - Use logical properties instead of physical ones:
     ```css
     /* Instead of */
     margin-left: 10px;
     
     /* Use */
     margin-inline-start: 10px;
     ```
   - Handle text alignment appropriately:
     ```css
     .gallery-caption {
         text-align: start;
         /* Not left or right */
     }
     ```

3. **HTML Markup**
   - Add `dir="rtl"` attribute where needed
   - Use `lang` attribute with correct language code
   ```html
   <div class="gallery-item" dir="rtl" lang="ar">
   ```

4. **Special Considerations**
   - Numbers should remain LTR in RTL text
   - Phone numbers and dates maintain their original direction
   - Icons and arrows need to be mirrored
   - Navigation elements should flow RTL
   - Consider cultural preferences for image layouts

### Testing RTL Translations

1. Enable RTL in WordPress admin
2. Check all gallery layouts
3. Verify lightbox navigation
4. Test form input fields
5. Validate admin interface elements

## Common Translation Pitfalls

1. **String Length Issues**
   - Problem: Translations may be significantly longer/shorter than English
   - Solution: Test UI with maximum-length translations
   - Example: "Add" → "Hinzufügen" (German, much longer)

2. **Placeholder Mishandling**
   ```php
   // WRONG
   msgid "Found %d images"
   msgstr "Trouvé images %d"  // Incorrect placeholder position
   
   // RIGHT
   msgid "Found %d images"
   msgstr "%d images trouvées"
   ```

3. **HTML Tag Errors**
   ```php
   // WRONG
   msgid "Click <a href='%s'>here</a> to continue"
   msgstr "<a href='%s'>Cliquez ici</a pour continuer"  // Missing '>'
   
   // RIGHT
   msgid "Click <a href='%s'>here</a> to continue"
   msgstr "<a href='%s'>Cliquez ici</a> pour continuer"
   ```

4. **Inconsistent Terminology**
   - Using different terms for the same concept
   - Solution: Create a glossary for your language

5. **Cultural Assumptions**
   - Assuming same date/time formats
   - Assuming same name formats
   - Solution: Use WordPress localization functions

## Testing Checklist

### Functionality Testing
- [ ] All translated strings display correctly
- [ ] No missing translations
- [ ] Placeholders work correctly
- [ ] Links are functional
- [ ] Forms submit properly
- [ ] Error messages display correctly

### Visual Testing
- [ ] Text fits in buttons/menus
- [ ] No overlap in UI elements
- [ ] Proper text wrapping
- [ ] Consistent alignment
- [ ] RTL display (if applicable)

### Technical Testing
- [ ] Character encoding is correct
- [ ] Special characters display properly
- [ ] Line breaks preserved
- [ ] HTML renders correctly
- [ ] JavaScript functionality intact

### Content Testing
- [ ] Grammar and spelling
- [ ] Consistency in terminology
- [ ] Cultural appropriateness
- [ ] Context accuracy

### Performance Testing
- [ ] Page load times normal
- [ ] No JavaScript errors
- [ ] Memory usage normal
- [ ] No PHP errors

## Technical Details

### Translation File Structure

```po
# Translation metadata
msgid ""
msgstr ""
"Project-Id-Version: MMP Photo Minder Pro 1.0.0\n"
"POT-Creation-Date: 2024-03-20 10:00+0000\n"
"PO-Revision-Date: 2024-03-20 12:00+0000\n"
"Language: fr_FR\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Plural-Forms: nplurals=2; plural=(n > 1);\n"

# Translatable string
#: includes/class-post-types.php:24
msgid "Photo Galleries"
msgstr "Galeries Photos"
```

### File Types Explained

1. **POT Files (.pot)**
   - Template files
   - Contains original strings
   - No translations
   - Base for PO files

2. **PO Files (.po)**
   - Human-readable
   - Contains translations
   - Can be edited
   - Source for MO files

3. **MO Files (.mo)**
   - Machine-readable
   - Binary format
   - Used by WordPress
   - Generated from PO files

## WordPress Core Consistency

### Following WordPress Standards

1. **Use Official Glossaries**
   - Access the [WordPress Translation Project](https://translate.wordpress.org/)
   - Find your language's glossary
   - Follow established terms

2. **Common Terms**
   ```
   English     → Standard Translation
   Dashboard   → [Your language's standard term]
   Posts       → [Your language's standard term]
   Pages       → [Your language's standard term]
   Media       → [Your language's standard term]
   ```

3. **Interface Elements**
   - Match WordPress admin interface terminology
   - Use consistent button/action text
   - Follow WordPress capitalization rules

4. **Error Messages**
   - Use similar tone as WordPress core
   - Maintain helpful but concise style
   - Follow standard error message patterns

### Style Guide Alignment

1. **Tone and Voice**
   - Professional but friendly
   - Clear and concise
   - Consistent with WordPress admin

2. **Formatting**
   - Follow locale-specific date formats
   - Use appropriate number formatting
   - Maintain proper quotation marks

[Rest of the original content follows...]

## Support

Need help with translations?
- Open an issue on GitHub
- Email us at [support@example.com]
- Join our translation community on Slack: [invite link]

## Recognition

All translators will be credited in:
- The plugin's readme.txt file
- Our website's contributors page
- Release notes when your translation is included

Thank you for helping make MMP Photo Minder Pro accessible to more users worldwide!

---

## Locale Codes Reference

Common WordPress locale codes:
- French (France): `fr_FR`
- German: `de_DE`
- Spanish (Spain): `es_ES`
- Italian: `it_IT`
- Dutch: `nl_NL`
- Russian: `ru_RU`
- Japanese: `ja`
- Chinese (Simplified): `zh_CN`
- Chinese (Traditional): `zh_TW`
- Korean: `ko_KR`
- Arabic: `ar`
- Hebrew: `he_IL`

[Full list of WordPress locales](https://make.wordpress.org/polyglots/teams/)