# LeaveModal.vue Logic Guide

## Purpose
This component handles both the **creation** and **editing** of leave types.

## Why `useForm` instead of `ref`?
- We use Inertia's `useForm` to leverage built-in features like:
    - Automatic handling of server-side validation errors.(form.errors,form.processing, form.success )
    - `form.processing` state for loading indicators.
    - Simplified `form.post()`/`form.put()` submission.

## Why the `watch`?
- **Context:** The modal is reused for different leave types.
- **Sync:** The `watch` is necessary to reactively copy the `props.leave` data into the `form` object whenever the user triggers an "Edit" action. If user click modal for AL , then it displayal AL modal , and so on.
- **Immediate Execution:** `{ immediate: true }` ensures that if the modal is opened while the component is already mounted, it triggers the synchronization immediately.
- **Performance:** The `form.id !== newVal.id` check prevents unnecessary re-rendering if the prop changes but the ID is the same.

## Interaction with Database

- Probation: The `probation_period_months` is stored here to be validated against the employee's `join_date` during the application submission process.

- Check the Switch (is_calculated_by_experience):
  If OFF (Flat Rate): The system takes the value directly from leave_types.default_days.
  If ON (Tenure-Based): The system bypasses the default_days and queries the leave_tiers table.