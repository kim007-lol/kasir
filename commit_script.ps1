git reset
Remove-Item status.txt -ErrorAction SilentlyContinue
Remove-Item full_diff.txt -ErrorAction SilentlyContinue
Remove-Item full_diff_utf8.txt -ErrorAction SilentlyContinue

git add app/Console/Commands/AutoCancelExpiredBookings.php
git commit -m "feat: add command to auto-cancel expired pickup bookings"

git add app/Console/Kernel.php
git commit -m "feat: schedule auto-cancel bookings command"

git add app/Exports/PelangganTemplateExport.php
git commit -m "feat: add excel export template for pelanggan"

git add app/Imports/PelangganImport.php
git commit -m "feat: add excel import logic for pelanggan"

git add app/Http/Controllers/Auth/CustomerAuthController.php
git commit -m "refactor: remove customer registration logic, handled by admin now"

git add app/Http/Controllers/BookingController.php
git commit -m "fix(booking): defer stock deduction to confirm step and add payment details for delivery"

git add app/Http/Controllers/CashierBookingController.php
git commit -m "fix(cashier): handle pickup vs delivery workflow separately and deduct stock on confirm"

git add app/Http/Controllers/CashierItemController.php
git commit -m "refactor: apply dynamic cashier route prefix based on role"

git add app/Http/Controllers/CategoryController.php
git commit -m "refactor: apply dynamic cashier route prefix for categories based on role"

git add app/Http/Controllers/DashboardController.php
git commit -m "fix(dashboard): scope total transactions to today"

git add app/Http/Controllers/MemberController.php
git commit -m "refactor: apply dynamic cashier route prefix for members based on role"

git add app/Http/Controllers/SupplierController.php
git commit -m "refactor: apply dynamic cashier route prefix for suppliers based on role"

git add app/Http/Controllers/UserController.php
git commit -m "feat(user): add bulk import pelanggan and handle member auto-linking"

git add app/Http/Controllers/WarehouseController.php
git commit -m "refactor: apply dynamic cashier route prefix for warehouse based on role"

git add app/Models/Booking.php
git commit -m "feat: add pickup_time and amount_paid to booking model fillables"

git add database/migrations/2026_02_25_140029_make_email_nullable_in_users_table.php
git commit -m "chore: migration to make user email nullable"

git add database/migrations/2026_02_27_162650_add_amount_paid_to_bookings_table.php
git commit -m "chore: migration to add amount_paid to bookings table"

git add database/migrations/2026_02_27_174800_add_pickup_time_to_bookings_table.php
git commit -m "chore: migration to add pickup_time to bookings table"

git add database/seeders/TestCustomerSeeder.php
git commit -m "test: add TestCustomerSeeder for testing roles and bookings"

git add resources/views/auth/customer-login.blade.php
git commit -m "ui: update customer login view to reflect admin-created accounts"

git add resources/views/booking/checkout.blade.php
git commit -m "ui(booking): enhance checkout view including payment methods and pickup time warnings"

git add resources/views/cashier-items/edit.blade.php
git commit -m "ui: dynamic layout and routes for cashier items edit view"

git add resources/views/cashier/bookings/index.blade.php
git commit -m "ui(cashier): update booking index view for separate pickup/delivery actions"

git add resources/views/cashier/bookings/show.blade.php
git commit -m "ui(cashier): update booking detail view for separate pickup/delivery actions"

git add resources/views/cashier/stock/index.blade.php
git commit -m "ui(cashier): add edit and delete actions in cashier stock index view"

git add resources/views/categories/create.blade.php
git commit -m "ui: dynamic layout and routes for categories create view"

git add resources/views/categories/edit.blade.php
git commit -m "ui: dynamic layout and routes for categories edit view"

git add resources/views/categories/index.blade.php
git commit -m "ui: dynamic layout and routes for categories index view"

git add resources/views/dashboard/index.blade.php
git commit -m "ui(dashboard): update widget text to reflect today's stats"

git add resources/views/history/index.blade.php
git commit -m "ui: add auto submit behavior to history filters"

git add resources/views/landing.blade.php
git commit -m "ui: refine landing page content and remove static stats/registration link"

git add resources/views/layouts/cashier.blade.php
git commit -m "ui: add management dropdown links to cashier layout"

git add resources/views/members/create.blade.php
git commit -m "ui: dynamic layout and routes for members create view"

git add resources/views/members/edit.blade.php
git commit -m "ui: dynamic layout and routes for members edit view"

git add resources/views/members/index.blade.php
git commit -m "ui: dynamic layout and routes for members index view"

git add resources/views/members/show.blade.php
git commit -m "ui: dynamic layout and routes for members show view"

git add resources/views/reports/index.blade.php
git commit -m "ui: configure auto-submit functionality for reports filters"

git add resources/views/suppliers/create.blade.php
git commit -m "ui: dynamic layout and routes for suppliers create view"

git add resources/views/suppliers/edit.blade.php
git commit -m "ui: dynamic layout and routes for suppliers edit view"

git add resources/views/suppliers/index.blade.php
git commit -m "ui: dynamic layout and routes for suppliers index view"

git add resources/views/users/create.blade.php
git commit -m "ui(user): update create form removing email and handling optional phone"

git add resources/views/users/edit.blade.php
git commit -m "ui(user): update edit form removing email field"

git add resources/views/users/index.blade.php
git commit -m "ui(user): add import/export excel buttons and hints on user index"

git add resources/views/warehouse/create.blade.php
git commit -m "ui: dynamic layout and routes for warehouse create view"

git add resources/views/warehouse/edit.blade.php
git commit -m "ui: dynamic layout and routes for warehouse edit view"

git add resources/views/warehouse/index.blade.php
git commit -m "ui: dynamic layout and routes for warehouse index view"

git add routes/web.php
git commit -m "refactor: setup dynamic cashier routes and remove customer register route"

git add user_guide.md.resolved
git commit -m "docs: generate comprehensive user guide"
