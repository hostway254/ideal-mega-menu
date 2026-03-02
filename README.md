# Ideal Mega Menu

A powerful and customizable mega menu plugin for WordPress. Create beautiful, responsive mega menus with multi-column layouts, widgets, icons, images, and badges.

## Features

- **Multi-Column Mega Menus** — Convert any top-level dropdown into a full-width mega menu with 2–6 columns
- **Drag & Drop Integration** — Works seamlessly with the built-in WordPress menu editor (Appearance → Menus)
- **Custom Icons** — Add icon classes (Dashicons, Font Awesome, etc.) to any menu item
- **Image Support** — Upload or link images for menu items via the WordPress Media Library
- **Badges** — Add colorful badges (e.g., "New", "Sale") to highlight menu items
- **Widget Areas** — 6 dedicated widget areas for embedding any WordPress widget inside mega menu columns
- **Multiple Styles** — Choose from Default, Dark, Light, or Minimal menu styles
- **Animation Options** — Fade, Slide Down, Zoom, or None — with configurable speed
- **Hover or Click Trigger** — Choose how the mega menu opens on desktop
- **Fully Responsive** — Automatically switches to a mobile-friendly accordion menu below a configurable breakpoint
- **Keyboard Accessible** — Full keyboard navigation support (Enter, Space, Escape, Arrow keys, Tab)
- **Lightweight** — No jQuery dependency on the frontend; pure vanilla JavaScript

## Installation

1. Download or clone this repository into your WordPress `wp-content/plugins/` directory:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/hostway254/ideal-mega-menu.git
   ```
2. Activate the plugin in **Plugins → Installed Plugins**.
3. Navigate to **Mega Menu** in the WordPress admin sidebar to configure global settings.

## Usage

### Setting Up a Mega Menu

1. Go to **Appearance → Menus** in the WordPress admin.
2. Create or select a navigation menu.
3. Add menu items and organize them with sub-items (child items become columns).
4. Expand any **top-level** menu item to see the **Ideal Mega Menu Settings** panel.
5. Check **Enable Mega Menu** to turn that item's dropdown into a mega menu.
6. Select the number of **Columns** (2–6).
7. Add any second-level items — each becomes a column heading with its own sub-items.

### Menu Item Options

Each menu item includes these additional settings:

| Option | Description |
|---|---|
| **Enable Mega Menu** | Converts the dropdown into a mega menu panel (top-level items only) |
| **Columns** | Number of columns for the mega menu (2–6) |
| **Icon Class** | CSS class for an icon (e.g., `dashicons-admin-home`, `fa fa-star`) |
| **Image URL** | URL to an image, or use the **Upload Image** button |
| **Badge Text** | Short text displayed as a badge (e.g., "New", "Hot") |
| **Badge Color** | Color picker for the badge background |
| **Hide Menu Item Text** | Visually hide the title (useful for icon-only items) |
| **Disable Link** | Prevents the link from navigating (useful for column headings) |

### Global Settings (Mega Menu → Settings)

| Setting | Options | Default |
|---|---|---|
| **Menu Style** | Default, Dark, Light, Minimal | Default |
| **Animation** | None, Fade, Slide Down, Zoom | Fade |
| **Animation Speed** | 0–2000 ms | 300 ms |
| **Trigger Event** | Hover, Click | Hover |
| **Mobile Breakpoint** | 320–1200 px | 768 px |

## File Structure

```
ideal-mega-menu/
├── ideal-mega-menu.php              # Main plugin file
├── README.md                        # This file
├── includes/
│   ├── class-mega-menu-walker.php   # Custom Walker_Nav_Menu for mega menu rendering
│   ├── class-mega-menu-admin.php    # Admin settings, menu item fields, widget areas
│   └── class-mega-menu-frontend.php # Frontend asset loading and menu modification
├── assets/
│   ├── css/
│   │   ├── mega-menu.css            # Frontend styles
│   │   └── admin.css                # Admin styles
│   └── js/
│       ├── mega-menu.js             # Frontend JavaScript (vanilla JS)
│       └── admin.js                 # Admin JavaScript (jQuery for WP admin)
└── languages/                       # Translation files (i18n ready)
```

## Requirements

- WordPress 5.0 or later
- PHP 7.4 or later

## License

This plugin is licensed under the [GPL-2.0+](http://www.gnu.org/licenses/gpl-2.0.txt).

## Contributing

Contributions are welcome! Please open an issue or submit a pull request.

## Author

[Hostway254](https://github.com/hostway254)
