# Route Map Laravel -> CI4

## Public

- `GET /` -> `Public\HomeController::index`
- `GET /loa/request` -> `Public\LoaRequestController::create`
- `POST /loa/request` -> `Public\LoaRequestController::store`
- `GET /loa/status/{requestCode}` -> `Public\LoaRequestController::status`
- `GET /loa/v/{token}` -> `Public\LoaLetterController::show`
- `GET /loa/v/{token}/preview` -> `Public\LoaLetterController::preview`
- `GET /loa/v/{token}/download` -> `Public\LoaLetterController::download`
- `GET /loa/verify` -> `Public\LoaVerifyController::form`
- `POST /loa/verify` -> `Public\LoaVerifyController::submit`
- `GET /loa/verify/result` -> `Public\LoaVerifyController::result`

## Auth

- `GET /login` -> `Auth\LoginController::index`
- `POST /login` -> `Auth\LoginController::login`
- `POST /logout` -> `Auth\LoginController::logout`
- `GET/POST /forgot-password`, `GET/POST /reset-password` -> modul reset password

## Admin Core

- `GET /dashboard` -> `Admin\DashboardController::index`
- `GET /admin/loa-requests` -> `Admin\LoaRequestController::index`
- `GET /admin/loa-requests/export/csv` -> `Admin\LoaRequestController::exportCsv`
- `POST /admin/loa-requests/{id}/quick-approve` -> `quickApprove`
- `GET /admin/loa-requests/{id}` -> `show`
- `POST /admin/loa-requests/{id}/approve` -> `approve`
- `POST /admin/loa-requests/{id}/reject` -> `reject`

- `GET /admin/loa-letters` -> `Admin\LoaLetterController::index`
- `GET /admin/loa-letters/export/csv` -> `Admin\LoaLetterController::exportCsv`
- `GET /admin/loa-letters/{id}/edit` -> `edit`
- `PUT /admin/loa-letters/{id}` -> `update`
- `PUT /admin/loa-letters/{id}/regenerate` -> `regenerate`
- `DELETE /admin/loa-letters/{id}` -> `destroy`
- `POST /admin/loa-letters/bulk-delete` -> `bulkDelete`

## Admin Settings

- `GET /admin/journals` -> `Admin\JournalController::index`
- `GET/POST /admin/journals/create` -> `create/store`
- `GET/PUT /admin/journals/{id}/edit` -> `edit/update`
- `DELETE /admin/journals/{id}` -> `destroy`

- `GET /admin/publishers` -> `Admin\PublisherController::index`
- `GET/POST /admin/publishers/create` -> `create/store`
- `GET/PUT /admin/publishers/{id}/edit` -> `edit/update`
- `DELETE /admin/publishers/{id}` -> `destroy`

- `GET /admin/users` -> `Admin\UserController::index`
- `GET/POST /admin/users/create` -> `create/store`
- `GET/PUT /admin/users/{id}/edit` -> `edit/update`
- `PUT /admin/users/{id}/password` -> `updatePassword`

- `GET /admin/notifikasi` -> `Admin\NotificationController::index`
- `POST /admin/notifikasi/{id}/kirim-email` -> `sendEmail`
- `DELETE /admin/notifikasi/{id}` -> `destroy`

- `GET /superadmin/settings/journals` -> `Admin\Settings\JournalProfileController::index`
- `GET/POST /superadmin/settings/journals/create` -> `create/store`
- `GET/PUT /superadmin/settings/journals/{id}/edit` -> `edit/update`
- `DELETE /superadmin/settings/journals/{id}` -> `destroy`

## Filter/Guard di CI4 (Setara Middleware Laravel)

- `auth` -> CI4 Shield auth filter
- `role:superadmin,admin_jurnal` -> custom `RoleFilter`
- `can:superadmin-only` -> custom `GateFilter` atau role check langsung
- throttle -> `Throttler` per endpoint penting (loa request, pdf preview/download, verify)

