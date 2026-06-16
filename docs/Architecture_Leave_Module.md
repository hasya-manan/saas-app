Technical Documentation: Leave System Architecture
Core Concepts
LeaveType (The "Folder"): Acts as the parent definition. It stores global policy settings like name, code, and probation_period_months.

LeaveTier (The "File"): Acts as the specific rule. It stores the calculated values that depend on seniority (e.g., min_years, max_years, allowed_days).

The Folder/File Analogy
LeaveType (Folder): Think of this as the "Annual Leave" folder. It tells the system the basic category.

LeaveTier (File): These are the specific rule sheets inside the folder.

File 1: "For employees with 0–3 years, use 13 days."

File 2: "For employees with 3–5 years, use 15 days."

File 3: "For employees with 5+ years, use 20 days."