Tenancy Architecture:  Notes
1. The Middleware: The "Bouncer" (Access Control)
Analogy: Think of Middleware as a security guard standing at the front door of your routes.

Roles:

CheckSuperAdmin: Only people with role_id === 1 can enter.

CheckAdminCompany: Only role_id === 2 can enter.

Function: It controls WHO can enter a specific URL. It doesn't look at the data yet; it only checks the user’s "ID card" (role_id).

For example 
    1.1 Middleware (The Login & Entry Check)
      a.  Happens on every single request to a specific route. (update , delete , login and etc..) because when login it already remember the session so everytime we do request it auto check the role ID .
      
      b. Every move after: The Middleware checks the "ID Card" (role_id) before the Controller even starts working.

    Result: It stops the wrong people from entering the room.If it try to access it will display forbidden.

2. The TenantScope: The "Filter" (Data Privacy)

The Filter: It automatically injects a WHERE tenant_id = X clause into every single SELECT query.

For Example 
    2.1 What it does: It automatically modifies every SQL query.

        a. The Logic: Even if you write a simple command like Department::all(), the Scope intercepts it.

        b. The Result:

            b.1 What you wrote: SELECT * FROM departments

            b.2 What actually happens: SELECT * FROM departments WHERE tenant_id = 5

The Backdoor: It includes a smart check: if a Super Admin is logged in, the filter is removed, allowing them to see data globally.

3. The Trait
The Trait is the bridge between your Model (the code) and the Scope (the logic). Without the Trait, the Model wouldn't know the Scope exists.

Key Features:

    A. The Glue (Reading)
        When the Model "boots up" (starts working), the Trait tells it: "Hey, you must use the TenantScope for everything you do." This saves you from having to manually add the scope to every single Model file.

    B. The Auto-Stamper (Creating)
        When you create a new record, the Trait acts like an automatic rubber stamp.

        The Logic: It waits for the creating event.

        The Action: It looks at the person logged in, grabs their tenant_id, and "stamps" it onto the new data before it hits the database.

Why it's great: You don't have to write $request->tenant_id in the Controller. It happens automatically in the background.
addGlobalScope: It ensures that every time the Model talks to the database, it follows the privacy rules (TenantScope).

4. Rules ( validation exist )

4.1. Validation vs. Global Scopes
Laravel's default exists:table,column validation rule uses a raw database query. It does not know about the TenantScope because validation usually happens before a Model is even involved.

Without the TenantExists rule, an Admin from Company A could send an ID belonging to Company B. The default validator would see the ID exists in the database and say "OK!", leading to data corruption. Your custom rule is the "Guard" at the front door.

5. Why remove role_id from $fillable?
It's all about Mass Assignment Vulnerability.

Imagine a hacker or a curious user opens the Browser DevTools and manually adds an input to your form: <input name="role_id" value="1">.

If role_id is in $fillable: When you run $user->update($request->all()), Laravel will see the 1 and automatically update that user to a SuperAdmin. They just hacked your system.

If role_id is NOT in $fillable: Laravel will see the 1, compare it against the $fillable list, see it's not there, and ignore it completely. The user stays a regular staff member.

in progess ( remove role_id and tenant_id for security purpose )
need to change inside logic controller and services that use role_id 