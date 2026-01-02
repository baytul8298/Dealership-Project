# Design System Guide

This guide provides comprehensive documentation for the dealership project's design system, including all reusable components, CSS utilities, and best practices.

## Table of Contents

1. [Design Tokens](#design-tokens)
2. [Components](#components)
3. [CSS Utilities](#css-utilities)
4. [Usage Examples](#usage-examples)
5. [Best Practices](#best-practices)

---

## Design Tokens

All design tokens are defined in `resources/css/app.css` using CSS custom properties and Tailwind utilities.

### Colors

```css
Primary: #ff8800 (Orange)
Primary Hover: #ff9900
Success: #10b981 (Green)
Error: #ef4444 (Red)
Warning: #f59e0b (Yellow)
Info: #3b82f6 (Blue)
```

### Usage in Components

```vue
<!-- Using CSS classes -->
<div class="bg-primary text-white">Primary background</div>
<div class="text-primary">Primary text</div>
<div class="border-primary">Primary border</div>

<!-- Using CSS variables -->
<div style="background-color: var(--color-primary)">Custom usage</div>
```

---

## Components

All shared components are located in `resources/js/Components/shared/`.

### 1. FormInput

Text input with floating label and orange focus state.

**Props:**
- `label` (string, required): Label text
- `modelValue` (string | number, required): v-model value
- `type` (string, optional): Input type (default: 'text')
- `placeholder` (string, optional): Placeholder text
- `required` (boolean, optional): Mark as required
- `error` (string, optional): Error message
- `disabled` (boolean, optional): Disable input

**Usage:**

```vue
<script setup>
import FormInput from '@/Components/shared/FormInput.vue'
import { ref } from 'vue'

const name = ref('')
const email = ref('')
const errorMessage = ref('')
</script>

<template>
  <FormInput
    v-model="name"
    label="Name (English)"
    placeholder="Enter your name"
    required
  />

  <FormInput
    v-model="email"
    label="Email"
    type="email"
    :error="errorMessage"
  />
</template>
```

**Features:**
- Floating label that moves up when focused or has value
- Orange left border (3px) on focus
- Orange label color on focus
- Error state with red border and error message

---

### 2. SearchableDropdown

Searchable dropdown with autocomplete functionality.

**Props:**
- `label` (string, required): Label text
- `modelValue` (string | number, required): v-model value
- `options` (Array<{value, label}>, required): Options list
- `placeholder` (string, optional): Placeholder text
- `required` (boolean, optional): Mark as required
- `error` (string, optional): Error message
- `disabled` (boolean, optional): Disable dropdown
- `searchPlaceholder` (string, optional): Search input placeholder

**Usage:**

```vue
<script setup>
import SearchableDropdown from '@/Components/shared/SearchableDropdown.vue'
import { ref } from 'vue'

const branch = ref('')
const branches = [
  { value: 1, label: 'Forkiva West Paxtonmouth Branch' },
  { value: 2, label: 'Forkiva Connshire Branch' },
  { value: 3, label: 'Forkiva DuBuquestad Branch' },
]
</script>

<template>
  <SearchableDropdown
    v-model="branch"
    label="Branch"
    :options="branches"
    required
  />
</template>
```

**Features:**
- Real-time search filtering
- Keyboard navigation
- Orange border on focus
- Checkmark icon for selected item
- Smooth animations

---

### 3. Checkbox

Checkbox with orange checked state.

**Props:**
- `modelValue` (boolean | array, required): v-model value
- `value` (string | number, optional): Value when used in array
- `label` (string, optional): Label text
- `disabled` (boolean, optional): Disable checkbox
- `indeterminate` (boolean, optional): Indeterminate state

**Usage:**

```vue
<script setup>
import Checkbox from '@/Components/shared/Checkbox.vue'
import { ref } from 'vue'

const agreed = ref(false)
const selectedIds = ref([])
</script>

<template>
  <!-- Single checkbox -->
  <Checkbox v-model="agreed" label="I agree to the terms" />

  <!-- Checkbox array (for lists) -->
  <Checkbox
    v-for="item in items"
    :key="item.id"
    v-model="selectedIds"
    :value="item.id"
    :label="item.name"
  />

  <!-- Indeterminate state (for "select all") -->
  <Checkbox
    v-model="selectAll"
    :indeterminate="someSelected"
    label="Select All"
  />
</template>
```

**Features:**
- Orange background when checked
- White checkmark icon
- Supports indeterminate state
- Works with single boolean or array values

---

### 4. DatePicker

Date picker with orange theme and calendar.

**Props:**
- `label` (string, required): Label text
- `modelValue` (Date | string | null, required): v-model value
- `placeholder` (string, optional): Placeholder text
- `required` (boolean, optional): Mark as required
- `error` (string, optional): Error message
- `disabled` (boolean, optional): Disable picker
- `minDate` (Date, optional): Minimum selectable date
- `maxDate` (Date, optional): Maximum selectable date

**Usage:**

```vue
<script setup>
import DatePicker from '@/Components/shared/DatePicker.vue'
import { ref } from 'vue'

const selectedDate = ref(null)
const fromDate = ref(null)
const toDate = ref(null)
</script>

<template>
  <DatePicker
    v-model="selectedDate"
    label="Select Date"
    required
  />

  <div class="grid grid-cols-2 gap-4">
    <DatePicker v-model="fromDate" label="From" />
    <DatePicker v-model="toDate" label="To" :min-date="fromDate" />
  </div>
</template>
```

**Features:**
- Orange header with "SELECT DATE" label
- Calendar with month navigation
- Orange highlighting for selected date
- Today indicator with orange border
- Smooth animations

---

### 5. DataTable

Advanced data table with search, filters, sorting, and pagination.

**Props:**
- `data` (array, required): Table data
- `columns` (ColumnDef[], required): Column definitions
- `searchable` (boolean, optional): Enable search (default: true)
- `searchPlaceholder` (string, optional): Search placeholder
- `selectable` (boolean, optional): Enable row selection (default: true)
- `filterable` (boolean, optional): Enable filters sidebar (default: true)
- `loading` (boolean, optional): Show loading state
- `pageSize` (number, optional): Initial page size (default: 10)
- `pageSizeOptions` (number[], optional): Page size options

**Events:**
- `row-select`: Emitted when rows are selected
- `row-click`: Emitted when a row is clicked

**Usage:**

```vue
<script setup>
import DataTable from '@/Components/shared/DataTable.vue'
import Checkbox from '@/Components/shared/Checkbox.vue'
import Badge from '@/Components/shared/Badge.vue'
import { ref } from 'vue'

const selectedRows = ref([])

const columns = [
  {
    id: 'select',
    header: ({ table }) => (
      <Checkbox
        v-model={table.getIsAllRowsSelected()}
        indeterminate={table.getIsSomeRowsSelected()}
      />
    ),
    cell: ({ row }) => (
      <Checkbox v-model={row.getIsSelected()} />
    ),
    size: 50,
  },
  {
    accessorKey: 'name',
    header: 'Name',
  },
  {
    accessorKey: 'phone',
    header: 'Phone',
  },
  {
    accessorKey: 'status',
    header: 'Status',
    cell: ({ row }) => (
      <Badge variant="success">
        {row.original.status}
      </Badge>
    ),
  },
  {
    accessorKey: 'created_at',
    header: 'Created at',
  },
]

const data = ref([
  { id: 1, name: 'Demo Customer', phone: '+962 7 9299 2222', status: 'Active', created_at: '2025-12-25 05:50 PM' },
  // ... more data
])
</script>

<template>
  <DataTable
    :data="data"
    :columns="columns"
    search-placeholder="Search customers..."
    @row-select="selectedRows = $event"
  >
    <template #filters="{ table }">
      <!-- Custom filter components -->
      <SearchableDropdown
        v-model="statusFilter"
        label="Status"
        :options="statusOptions"
      />
      <DatePicker v-model="fromDate" label="From" />
      <DatePicker v-model="toDate" label="To" />
    </template>
  </DataTable>
</template>
```

**Features:**
- Search across all columns
- Column sorting
- Row selection with checkboxes
- Pagination with customizable page sizes
- Filter sidebar (customizable via slot)
- Loading state
- Responsive design

---

### 6. Badge

Status badge with different variants.

**Props:**
- `variant` ('success' | 'error' | 'warning' | 'info' | 'default', optional): Badge style
- `icon` (boolean, optional): Show icon
- `size` ('sm' | 'md' | 'lg', optional): Badge size

**Usage:**

```vue
<script setup>
import Badge from '@/Components/shared/Badge.vue'
</script>

<template>
  <Badge variant="success">Active</Badge>
  <Badge variant="error">Inactive</Badge>
  <Badge variant="warning">Pending</Badge>
  <Badge variant="info" icon>New</Badge>
</template>
```

---

### 7. Button

Standardized button component.

**Props:**
- `variant` ('primary' | 'secondary' | 'outline' | 'ghost' | 'danger', optional): Button style
- `size` ('sm' | 'md' | 'lg', optional): Button size
- `type` ('button' | 'submit' | 'reset', optional): Button type
- `disabled` (boolean, optional): Disable button
- `loading` (boolean, optional): Show loading state
- `fullWidth` (boolean, optional): Full width button

**Usage:**

```vue
<script setup>
import Button from '@/Components/shared/Button.vue'
import { ref } from 'vue'

const isLoading = ref(false)

async function handleSubmit() {
  isLoading.value = true
  // API call
  isLoading.value = false
}
</script>

<template>
  <Button variant="primary" @click="handleSubmit" :loading="isLoading">
    Save Changes
  </Button>

  <Button variant="outline">Cancel</Button>

  <Button variant="danger" size="sm">Delete</Button>
</template>
```

---

### 8. TextArea

Multi-line text input with floating label.

**Props:**
- `label` (string, required): Label text
- `modelValue` (string, required): v-model value
- `placeholder` (string, optional): Placeholder text
- `required` (boolean, optional): Mark as required
- `error` (string, optional): Error message
- `disabled` (boolean, optional): Disable textarea
- `rows` (number, optional): Number of rows (default: 4)
- `maxLength` (number, optional): Maximum character length

**Usage:**

```vue
<script setup>
import TextArea from '@/Components/shared/TextArea.vue'
import { ref } from 'vue'

const description = ref('')
</script>

<template>
  <TextArea
    v-model="description"
    label="Description"
    :max-length="500"
    required
  />
</template>
```

---

## CSS Utilities

### Form Input Classes

```css
.input-base        /* Base input styling */
.input-focus       /* Orange focus effect with left border */
.input-error       /* Error state styling */
```

### Label Classes

```css
.label-base        /* Base label styling */
.label-floating    /* Floating label position */
.label-active      /* Active label color (orange) */
```

### Button Classes

```css
.btn-base          /* Base button styling */
.btn-primary       /* Primary orange button */
.btn-secondary     /* Secondary gray button */
.btn-outline       /* Outline button with orange border */
```

### Table Classes

```css
.table-base        /* Base table styling */
.table-header      /* Table header styling */
.table-row         /* Table row with hover effect */
.table-cell        /* Table cell padding and text */
```

### Badge Classes

```css
.badge-base        /* Base badge styling */
.badge-success     /* Green success badge */
.badge-error       /* Red error badge */
.badge-warning     /* Yellow warning badge */
.badge-info        /* Blue info badge */
```

### Card Classes

```css
.card-base         /* Card container */
.card-header       /* Card header section */
.card-body         /* Card body section */
```

### Dropdown Classes

```css
.dropdown-base     /* Dropdown container */
.dropdown-item     /* Dropdown item */
.dropdown-item-active  /* Active dropdown item */
```

### Utility Classes

```css
.transition-base   /* Standard transition timing */
.text-primary      /* Primary orange text */
.bg-primary        /* Primary orange background */
.border-primary    /* Primary orange border */
.ring-primary      /* Primary orange focus ring */
```

---

## Usage Examples

### Complete Form Example

```vue
<script setup lang="ts">
import { ref } from 'vue'
import FormInput from '@/Components/shared/FormInput.vue'
import SearchableDropdown from '@/Components/shared/SearchableDropdown.vue'
import DatePicker from '@/Components/shared/DatePicker.vue'
import TextArea from '@/Components/shared/TextArea.vue'
import Checkbox from '@/Components/shared/Checkbox.vue'
import Button from '@/Components/shared/Button.vue'

const form = ref({
  name: '',
  email: '',
  branch: '',
  date: null,
  description: '',
  agreed: false,
})

const branches = [
  { value: 1, label: 'Branch 1' },
  { value: 2, label: 'Branch 2' },
]

const isSubmitting = ref(false)
const errors = ref({})

async function handleSubmit() {
  isSubmitting.value = true
  // Submit form
  isSubmitting.value = false
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
      <FormInput
        v-model="form.name"
        label="Name"
        required
        :error="errors.name"
      />

      <FormInput
        v-model="form.email"
        label="Email"
        type="email"
        required
        :error="errors.email"
      />
    </div>

    <SearchableDropdown
      v-model="form.branch"
      label="Branch"
      :options="branches"
      required
      :error="errors.branch"
    />

    <DatePicker
      v-model="form.date"
      label="Date"
      required
      :error="errors.date"
    />

    <TextArea
      v-model="form.description"
      label="Description"
      :max-length="500"
      :error="errors.description"
    />

    <Checkbox
      v-model="form.agreed"
      label="I agree to the terms and conditions"
    />

    <div class="flex gap-2">
      <Button type="submit" variant="primary" :loading="isSubmitting">
        Submit
      </Button>
      <Button type="button" variant="outline">
        Cancel
      </Button>
    </div>
  </form>
</template>
```

### Complete Table Example

```vue
<script setup lang="ts">
import { ref } from 'vue'
import DataTable from '@/Components/shared/DataTable.vue'
import Badge from '@/Components/shared/Badge.vue'
import Checkbox from '@/Components/shared/Checkbox.vue'
import Button from '@/Components/shared/Button.vue'
import SearchableDropdown from '@/Components/shared/SearchableDropdown.vue'
import DatePicker from '@/Components/shared/DatePicker.vue'

const customers = ref([
  {
    id: 1,
    name: 'Demo Customer',
    phone: '+962 7 9299 2222',
    status: 'Active',
    created_at: '2025-12-25 05:50 PM',
    updated_at: '2025-12-25 05:50 PM',
  },
  // ... more data
])

const statusFilter = ref('')
const fromDate = ref(null)
const toDate = ref(null)

const columns = [
  {
    id: 'select',
    header: ({ table }) => (
      <Checkbox
        checked={table.getIsAllRowsSelected()}
        indeterminate={table.getIsSomeRowsSelected()}
        onChange={table.getToggleAllRowsSelectedHandler()}
      />
    ),
    cell: ({ row }) => (
      <Checkbox
        checked={row.getIsSelected()}
        onChange={row.getToggleSelectedHandler()}
      />
    ),
    size: 50,
  },
  {
    accessorKey: 'name',
    header: 'Name',
  },
  {
    accessorKey: 'phone',
    header: 'Phone',
  },
  {
    accessorKey: 'status',
    header: 'Status',
    cell: ({ row }) => {
      const status = row.original.status
      return (
        <Badge variant={status === 'Active' ? 'success' : 'error'}>
          {status}
        </Badge>
      )
    },
  },
  {
    accessorKey: 'created_at',
    header: 'Created at',
  },
  {
    accessorKey: 'updated_at',
    header: 'Updated at',
  },
]

function handleRowClick(row) {
  console.log('Row clicked:', row)
}

function handleRowSelect(rows) {
  console.log('Selected rows:', rows)
}
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
      <Button variant="primary">Add Customer</Button>
    </div>

    <DataTable
      :data="customers"
      :columns="columns"
      search-placeholder="Search customers..."
      @row-click="handleRowClick"
      @row-select="handleRowSelect"
    >
      <template #filters>
        <SearchableDropdown
          v-model="statusFilter"
          label="Status"
          :options="[
            { value: 'active', label: 'Active' },
            { value: 'inactive', label: 'Inactive' },
          ]"
        />

        <DatePicker v-model="fromDate" label="From" />
        <DatePicker v-model="toDate" label="To" />
      </template>
    </DataTable>
  </div>
</template>
```

---

## Best Practices

### 1. Always Use Shared Components

Instead of creating custom inputs, dropdowns, etc., always use the shared components for consistency.

```vue
<!-- ✅ Good -->
<FormInput v-model="name" label="Name" />

<!-- ❌ Bad -->
<input type="text" v-model="name" class="..." />
```

### 2. Use CSS Utility Classes

Leverage the predefined CSS classes for common patterns.

```vue
<!-- ✅ Good -->
<button class="btn-base btn-primary">Save</button>

<!-- ❌ Bad -->
<button class="px-4 py-2 bg-orange-500 text-white rounded-lg...">Save</button>
```

### 3. Consistent Color Usage

Always use the design system colors instead of arbitrary values.

```vue
<!-- ✅ Good -->
<div class="text-primary">Orange text</div>

<!-- ❌ Bad -->
<div class="text-orange-500">Orange text</div>
```

### 4. Component Composition

Build complex UIs by composing simple components.

```vue
<template>
  <div class="card-base">
    <div class="card-header">
      <h2>Customer Information</h2>
    </div>
    <div class="card-body space-y-4">
      <FormInput v-model="name" label="Name" />
      <SearchableDropdown v-model="branch" label="Branch" :options="branches" />
    </div>
  </div>
</template>
```

### 5. Error Handling

Always pass error messages to form components for consistent error display.

```vue
<FormInput
  v-model="email"
  label="Email"
  type="email"
  :error="errors.email"
/>
```

### 6. Loading States

Use the loading prop on buttons during async operations.

```vue
<Button :loading="isSubmitting" @click="handleSubmit">
  Save
</Button>
```

---

## Migration Guide

To update existing components to use the new design system:

1. Replace custom inputs with `FormInput`
2. Replace select elements with `SearchableDropdown`
3. Replace custom checkboxes with `Checkbox`
4. Replace date inputs with `DatePicker`
5. Replace custom buttons with `Button`
6. Replace status indicators with `Badge`
7. Add appropriate CSS utility classes
8. Remove custom inline styles

### Example Migration

**Before:**

```vue
<input
  type="text"
  v-model="name"
  class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg"
/>
```

**After:**

```vue
<FormInput v-model="name" label="Name" />
```

---

## Support

For questions or issues with the design system, please refer to:
- This documentation
- Component source code in `resources/js/Components/shared/`
- CSS utilities in `resources/css/app.css`
