# FX Builder 1.5.1 Release Notes

## Overview
Version 1.5.1 is a maintenance and bug fix release that improves user experience with modal interactions, fixes dragging functionality, and resolves several edge cases.

## Key Changes

### User Experience Improvements

**Modal Cancel Functionality**
- Added "Cancel" button to both Edit Content and Row Settings modals
- Users can now discard changes without saving when editing content or row settings
- Original content is preserved and restored when canceling, preventing accidental data loss
- Modal buttons now clearly labeled as "Cancel" and "Save & Close" for better clarity

**Fixed Height Row Overflow**
- Rows with fixed height now automatically apply `overflow: hidden` to prevent content from spilling outside the container
- Ensures consistent visual appearance when using fixed height rows

### Bug Fixes

**Sortable.js Dragging Issues**
- Fixed invalid selector error (`'>*' is not a valid selector`) that prevented rows and columns from being dragged
- Explicitly configured Sortable.js with `filter: null` and `draggable` selectors for both rows and columns
- Row and column reordering now works reliably across all browsers

**Font Loading Optimization**
- Fixed fonts being enqueued even when no fonts are selected in the settings
- Reduces unnecessary HTTP requests and improves page load performance
