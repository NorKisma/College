# Student Management System Enhancements

## Overview
This project update focuses on enhancing the user interface and experience of the Student Management module. The primary improvement is the integration of **Tom Select**, a powerful and lightweight library, to replace standard dropdown menus with modern, searchable, and stylable select components.

## Key Features

### 1. Searchable Dropdowns (Tom Select)
*   **Context**: Applied to "Class", "Gender", and "Section" dropdowns in the following areas:
    *   **Student Registration (Add/Edit Modals)**: Allows for quick selection of classes and gender.
    *   **Student List Filters**: Enables efficient filtering of the student table by class, gender, or section.
*   **Functionality**:
    *   **Live Search**: Users can type to filter options instantly.
    *   **Keyboard Navigation**: Full support for arrow keys and enter to select.
    *   **Placeholder Support**: "Select Class" and "Select Gender" placeholders are properly displayed.

### 2. UI/UX Improvements
*   **Visual Consistency**: The Tom Select components have been customized to match the existing application theme (Tailwind CSS based).
    *   **Rounded Corners**: Inputs feature `rounded-xl` borders to match other form elements.
    *   **Dark Mode Support**: Full compatibility with the application's dark mode, updating background and text colors dynamically.
    *   **Standard Caret**: Restored the standard chevron (caret) appearance to match the design requirements (screenshot).
*   **Modal Compatibility**:
    *   **Z-Index Fix**: Addressed z-index issues to ensure dropdowns appear above modal windows.
    *   **Overflow Handling**: Configured `dropdownParent: 'body'` to prevent clipping within scrollable modals.

### 3. Code Refactoring & Cleanup
*   **Unified Initialization**: A single, robust JavaScript initialization block now handles all `.tom-select-class` elements.
*   **Dynamic value setting**: Updated `openEdit` functions to programmatically set Tom Select values when editing student records, ensuring data is correctly pre-loaded.
*   **Translation Support**: added translation keys for "Select Gender" to support multi-language (English/Arabic) requirements.

## Technical Implementation Details

### Modified Files
*   `views/admin/students/students.php`: Main student management interface.

### CSS Customization
Custom CSS was added to `students.php` to override default Tom Select styles:
```css
/* Tom Select Tailwind Tweaks */
.ts-control {
    border-radius: 0.75rem !important;
    padding: 0.75rem 1rem !important;
    border-color: #d1d5db !important;
    /* ... other styles */
}
/* Dark mode overrides */
.dark .ts-control {
    background-color: #1f2937 !important;
    border-color: #374151 !important;
    color: #fff !important;
}
```

### JavaScript Initialization
```javascript
document.querySelectorAll('.tom-select-class').forEach((el) => {
    new TomSelect(el, {
        create: false,
        sortField: { field: "text", direction: "asc" },
        plugins: [], // Standard caret style
        searchField: ['text'],
        dropdownParent: 'body'
    });
});
```

## How to Use
1.  **Add Student**: Click "Add New Student". The "Class" and "Gender" fields are now searchable dropdowns.
2.  **Edit Student**: Click "Edit" on a student row. The dropdowns are pre-filled with the student's current data and remain searchable.
3.  **Filter List**: Use the dropdowns at the top of the student list to filter by Class, Gender, or Section.

## Requirements
*   **Tom Select Library**: loaded via CDN (already included in `students.php`).
*   **Tailwind CSS**: Used for styling classes.
