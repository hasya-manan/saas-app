# Leave Approvals & Supervisor Logic Notes

## 1. How the system knows who can access the Leave Approvals page
We allow access only to Company Admins (`role_id === 2`) or users who manage other staff (`is_supervisor`).

* **Inside `HandleInertiaRequests.php`:** We add a dynamic check:
  ```php
  'is_supervisor' => \App\Models\User::where('supervisor_id', $request->user()->id)->exists(),

## 2. Example Walkthrough: Why and How it Works

### Your Table Data Scenario:
* **Person M** (`id: 2`, `supervisor_id: NULL`) -> Person M has no boss.
* **Person A** (`id: 4`, `supervisor_id: 2`) -> Person A reports to Person M.

### Who is who?
* **Person A** is a subordinate (they report to someone). They cannot approve anyone's leave.
* **Person M** is the supervisor (Person A reports to them). Person M can approve leave.

---

### Step-by-Step Check:

#### When Person M (`id: 2`) logs in:
1. The middleware runs the check: `\App\Models\User::where('supervisor_id', 2)->exists()`
2. It looks at the database and asks: *"Does anyone have 2 in their supervisor_id column?"*
3. **Yes!** Person A has 2. So it returns `true`.
4. In your Vue sidebar, `$page.props.auth.user.is_supervisor` becomes `true`, and Person M sees the "Leave Approvals" button.

#### When Person A (`id: 4`) logs in:
1. The middleware runs the check: `\App\Models\User::where('supervisor_id', 4)->exists()`
2. It looks at the database and asks: *"Does anyone have 4 in their supervisor_id column?"*
3. **No one** reports to Person A. So it returns `false`.
4. In your Vue sidebar, `$page.props.auth.user.is_supervisor` becomes `false`, and Person A does not see the button.