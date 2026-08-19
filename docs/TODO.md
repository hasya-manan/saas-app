# Leave Type Management Tasks (Superadmin / Company Admin)

## 1. Soft Delete
- [ ] Add a "Delete" action button to active leave types in the admin panel.
- [ ] Implement the delete controller method using Laravel's soft delete:
  ```php
  
  # Public Holiday Implementation Tasks

- [ ] **Database Table**: Create `public_holidays` migration
    - Fields: `id`, `tenant_id`, `name` (string), `holiday_date` (date), `timestamps`.
- [ ] **Admin Controller**: Create `PublicHolidayController`
    - Implement `index`, `store`, `edit`, `update`, and `destroy` methods for CRUD operations.
- [ ] **Logic Update**: Modify leave application `store` method
    - Implement a `while` loop that iterates from `start_date` to `end_date`.
    - Check if current day `!isWeekend()`.
    - Query `public_holidays` table and check if the current date `!in_array()` of holiday dates.
    - Increment `totalDays` only if both conditions are met.