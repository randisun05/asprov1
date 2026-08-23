<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('Index', [

//     ]);
// })->name('/');

Route::get('/', [\App\Http\Controllers\Public\PublicController::class, 'index'])->name('/');

//prefix "admin"
Route::prefix('admin')->group(function() {

    //middleware "auth"
    Route::group(['middleware' => ['auth', 'two-factor.required']], function () {
        //route dashboard

        Route::get('/dashboard', App\Http\Controllers\Admin\DashboardController::class)->name('admin.dashboard');
        Route::get('/security/two-factor', function () {
            $user = auth()->user();

            return \Inertia\Inertia::render('Admin/Security/TwoFactor', [
                'enabled' => !is_null($user->two_factor_secret),
                'confirmed' => !is_null($user->two_factor_confirmed_at),
                'mustEnable' => in_array($user->role, ['administrator', 'sekretariat'], true),
            ]);
        })->name('admin.security.two-factor');
        Route::resource('/management', App\Http\Controllers\Admin\ManagementController::class, ['as' => 'admin']);
        Route::resource('/docudigi', App\Http\Controllers\Admin\DocuDigiController::class, ['as' => 'admin']);
        Route::post('/docudigi/{id}/paraf', [\App\Http\Controllers\Admin\DocuDigiController::class, 'paraf'])->name('admin.docudigi.paraf');
        Route::post('/docudigi/{id}/approve', [\App\Http\Controllers\Admin\DocuDigiController::class, 'approve'])->name('admin.docudigi.approve');
        Route::post('/docudigi/{id}/cancel', [\App\Http\Controllers\Admin\DocuDigiController::class, 'cancel'])->name('admin.docudigi.cancel');
        Route::get('/managementfilter', [\App\Http\Controllers\Admin\ManagementController::class, 'filter'])->name('admin.menegemnet.filter');
        Route::post('/management/store', [\App\Http\Controllers\Admin\ManagementController::class, 'store'])->name('admin.menegemnet.store');
        Route::post('/management/update', [\App\Http\Controllers\Admin\ManagementController::class, 'update'])->name('admin.menegemnet.update');
        Route::post('/management/update/status', [\App\Http\Controllers\Admin\ManagementController::class, 'status'])->name('admin.menegemnet.status');
        Route::middleware('role:administrator,keanggotaan')->group(function () {
            Route::get('/registration/', [\App\Http\Controllers\Admin\RegistrationController::class, 'index'])->name('admin.registration.index');
            Route::post('/registration/store', [\App\Http\Controllers\Admin\RegistrationController::class, 'store'])->name('admin.registration.store');
            Route::get('/registration/group', [\App\Http\Controllers\Admin\RegistrationController::class, 'group'])->name('admin.registration.group');
            Route::post('/registration/group/{id}/done', [\App\Http\Controllers\Admin\RegistrationController::class, 'doneGroup'])->name('admin.registration.group.done');
            Route::post('/registration/group/{id}/confirm', [\App\Http\Controllers\Admin\RegistrationController::class, 'confirmGroup'])->name('admin.registration.group.confirm');
            Route::post('/registration/group/{id}/reject', [\App\Http\Controllers\Admin\RegistrationController::class, 'rejectGroup'])->name('admin.registration.group.reject');
            Route::post('/registration/import', [\App\Http\Controllers\Admin\RegistrationController::class, 'importStore'])->name('admin.registration.import.store');
            Route::get('/registration/import', [\App\Http\Controllers\Admin\RegistrationController::class, 'import'])->name('admin.registration.import');
            Route::post('/registration/{id}/email-approve', [\App\Http\Controllers\Admin\RegistrationController::class, 'Sendemailapprove'])->name('admin.registration.email.approve');
            Route::post('/registration/group/approve', [\App\Http\Controllers\Admin\RegistrationController::class, 'approveGroup'])->name('admin.registration.group.approve');
            Route::post('/registration/{id}/approve', [\App\Http\Controllers\Admin\RegistrationController::class, 'approve'])->name('admin.registration.approve');
            Route::post('/registration/{id}/approve-lb', [\App\Http\Controllers\Admin\RegistrationController::class, 'approveLB'])->name('admin.registration.approve.lb');
            Route::post('/registration/{id}/confirm', [\App\Http\Controllers\Admin\RegistrationController::class, 'confirm'])->name('admin.registration.confirm');
            Route::post('/registration/{id}/paid', [\App\Http\Controllers\Admin\RegistrationController::class, 'paid'])->name('admin.registration.paid');
            Route::post('/registration/{id}/reject', [\App\Http\Controllers\Admin\RegistrationController::class, 'reject'])->name('admin.registration.reject');
            Route::post('/registration/{id}/email', [\App\Http\Controllers\Admin\RegistrationController::class, 'sendEmail'])->name('admin.registration.sendEmail');
            Route::get('/registration/paid/export', [\App\Http\Controllers\Admin\RegistrationController::class, 'exportPaid'])->name('admin.registration.export');
            Route::get('/registration/data/export', [\App\Http\Controllers\Admin\RegistrationController::class, 'exportRegistration'])->name('admin.registration.data.export');
            Route::resource('/registration', \App\Http\Controllers\Admin\RegistrationController::class, ['as' => 'admin']);
        });

        Route::middleware('role:administrator,humas')->group(function () {
            Route::post('/announcements/{id}/status', [\App\Http\Controllers\Admin\AnnouncementController::class, 'status'])->name('admin.announcements.status');
            Route::resource('/announcements', \App\Http\Controllers\Admin\AnnouncementController::class, ['as' => 'admin']);

            Route::get('/admin/posts/', [\App\Http\Controllers\Admin\PostController::class, 'list'])->name('admin.posts.list');
            Route::post('/posts/{id}', [\App\Http\Controllers\Admin\PostController::class, 'update'])->name('admin.posts.update');
            Route::resource('/posts', \App\Http\Controllers\Admin\PostController::class, ['as' => 'admin']);
            Route::post('/posts/{id}/limited', [\App\Http\Controllers\Admin\PostController::class, 'limited'])->name('admin.posts.limited');
            Route::post('/posts/{id}/approve', [\App\Http\Controllers\Admin\PostController::class, 'approve'])->name('admin.posts.approve');
            Route::post('/posts/{id}/return', [\App\Http\Controllers\Admin\PostController::class, 'return'])->name('admin.posts.return');
            Route::post('/posts/{id}/reject', [\App\Http\Controllers\Admin\PostController::class, 'reject'])->name('admin.posts.reject');
            Route::post('/posts/{id}/cancel', [\App\Http\Controllers\Admin\PostController::class, 'cancel'])->name('admin.posts.cancel');
            Route::post('/posts/{id}/cancelLimited', [\App\Http\Controllers\Admin\PostController::class, 'cancelLimited'])->name('admin.posts.cancellimited');
            Route::post('/posts/{id}/submission', [\App\Http\Controllers\Admin\PostController::class, 'submission'])->name('admin.posts.submission');
            Route::get('/category/create', [\App\Http\Controllers\Admin\PostController::class, 'categoryCreate'])->name('admin.category.create');
            Route::post('/category/store', [\App\Http\Controllers\Admin\PostController::class, 'categoryStore'])->name('admin.category.store');
            Route::get('/category/{id}/edit', [\App\Http\Controllers\Admin\PostController::class, 'categoryEdit'])->name('admin.category.edit');
            Route::put('/category/{id}', [\App\Http\Controllers\Admin\PostController::class, 'categoryUpdate'])->name('admin.category.update');
            Route::delete('/category/{id}', [\App\Http\Controllers\Admin\PostController::class, 'categoryDestroy'])->name('admin.category.destroy');
        });

          Route::post('/events/{id}/generate-question', [\App\Http\Controllers\Admin\QuestionsController::class, 'EnrollQuestion'])->name('event.generate.question');
        Route::middleware('role:administrator,humas')->group(function () {
            Route::get('/events/{id}/certificates/import', [\App\Http\Controllers\Admin\EventController::class, 'certificatesImportCreate'])->name('admin.events.certificates.import.create');
            Route::post('/events/{id}/certificates/import', [\App\Http\Controllers\Admin\EventController::class, 'certificatesImportStore'])->name('admin.events.certificates.import.store');
            Route::get('/events/{id}/certificates', [\App\Http\Controllers\Admin\EventController::class, 'certificatesIndex'])->name('admin.events.certificates.index');
            Route::get('/events/{id}/certificates/create', [\App\Http\Controllers\Admin\EventController::class, 'certificatesCreate'])->name('admin.events.certificates.create');
            Route::post('/events/{id}/certificates/store', [\App\Http\Controllers\Admin\EventController::class, 'certificatesStore'])->name('admin.events.certificates.store');
            Route::get('/events/{event}/certificates/{id}', [\App\Http\Controllers\Admin\EventController::class, 'certificatesView'])->name('admin.events.certificates.view');
            Route::delete('/events/{event}/certificates/{id}/destroy', [\App\Http\Controllers\Admin\EventController::class, 'certificatesDestroy'])->name('admin.events.certificates.destroy');
            Route::get('/events/certificates/templates', [\App\Http\Controllers\Admin\EventController::class, 'certificatesTemplate'])->name('admin.events.certificates.template');
            Route::post('/events/certificates/templates/store', [\App\Http\Controllers\Admin\EventController::class, 'certificatesTemplateStore'])->name('admin.events.certificates.template.store');
            Route::delete('/events/certificates/templates/{id}', [\App\Http\Controllers\Admin\EventController::class, 'certificatesTemplateDelete'])->name('admin.events.certificates.template.delete');
            // 1. Route untuk menampilkan halaman form import (GET)
            Route::get('/events/{event}/certificates-excel', [App\Http\Controllers\Admin\EventController::class, 'certificatesExcelIndex'])->name('admin.events.certificates.import');
            // 2. Route untuk memproses file excel (POST)
            Route::post('/events/{event}/certificates-import', [App\Http\Controllers\Admin\EventController::class, 'certificatesExcelStore'])->name('admin.events.certificates.import.store');
            Route::post('/events/{id}', [\App\Http\Controllers\Admin\EventController::class, 'update'])->name('admin.events.update');
            Route::get('/events/{id}/export', [\App\Http\Controllers\Admin\EventController::class, 'exportParticipant'])->name('admin.events.export');
            Route::post('/events/{id}/change', [\App\Http\Controllers\Admin\EventController::class, 'change'])->name('admin.events.status.change');
            Route::post('/events/{id}/absen', [\App\Http\Controllers\Admin\EventController::class, 'absen'])->name('admin.events.status.absen');
            Route::post('/events/{id}/updaterole', [\App\Http\Controllers\Admin\EventController::class, 'updateRole'])->name('admin.events.updateRole');
            Route::post('/events/{id}/absenall', [\App\Http\Controllers\Admin\EventController::class, 'absenAll'])->name('admin.events.absenall');
            Route::get('/members/find/{nip}', [\App\Http\Controllers\Admin\EventController::class, 'findMemberByNip']);
            Route::post('/events/{id}/enroll', [\App\Http\Controllers\Admin\EventController::class, 'enrollMember'])->name('admin.events.enroll');
            Route::get('/events/{id}/questions', [\App\Http\Controllers\Admin\EventController::class, 'questionBank'])->name('admin.events.questions');
            Route::post('/events/{id}/questions/sync', [\App\Http\Controllers\Admin\EventController::class, 'syncQuestions'])->name('admin.events.questions.sync');
            Route::resource('/events', \App\Http\Controllers\Admin\EventController::class, ['as' => 'admin']);
            Route::post('/medias/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'update'])->name('admin.medias.update');
            Route::resource('/medias', \App\Http\Controllers\Admin\MediaController::class, ['as' => 'admin']);
        });

        Route::middleware('role:administrator,pendanaan')->group(function () {
            Route::post('/merchans/{id}', [\App\Http\Controllers\Admin\MerchanController::class, 'update'])->name('admin.merchans.update');
            Route::post('/merchans/{id}/change', [\App\Http\Controllers\Admin\MerchanController::class, 'change'])->name('admin.merchans.status');
            Route::resource('/merchans', \App\Http\Controllers\Admin\MerchanController::class, ['as' => 'admin']);
        });

        Route::middleware('role:administrator')->group(function () {
            Route::resource('/setting', \App\Http\Controllers\Admin\AuthAdminController::class, ['as' => 'admin']);
        });

        Route::get('/members/report/export', [\App\Http\Controllers\Admin\DataMembersController::class, 'exportReport'])->name('admin.member.export');
        Route::get('/members/report', [\App\Http\Controllers\Admin\DataMembersController::class, 'indexReport'])->name('admin.member.report');
        Route::get('/members/report/recap', [\App\Http\Controllers\Admin\DataMembersController::class, 'recapitulation'])->name('admin.member.report.recap');
        Route::resource('/members', \App\Http\Controllers\Admin\DataMembersController::class, ['as' => 'admin']);
        Route::post('/members/{id}/image', [\App\Http\Controllers\Admin\DataMembersController::class, 'updateImage'])->name('admin.members.image');
        Route::post('/members/{id}/points/reward', [\App\Http\Controllers\Admin\PointController::class, 'reward'])->name('admin.members.points.reward');
        Route::get('/points/reward-group', [\App\Http\Controllers\Admin\PointController::class, 'rewardGroupForm'])->name('admin.points.reward-group');
        Route::post('/points/reward-group', [\App\Http\Controllers\Admin\PointController::class, 'rewardGroup'])->name('admin.points.reward-group.store');

        Route::middleware('role:administrator')->group(function () {
            Route::resource('/achievements', \App\Http\Controllers\Admin\AchievementController::class, ['as' => 'admin']);
            Route::post('/achievements/{id}', [\App\Http\Controllers\Admin\AchievementController::class, 'update'])->name('admin.acievements.update');
            Route::post('/achievements/{id}/change', [\App\Http\Controllers\Admin\AchievementController::class, 'change'])->name('admin.achievements.change');
        });

        Route::post('/generate-qr', [\App\Http\Controllers\Admin\QRCodeController::class, 'generateQRCode'])->name('admin.qrcode.generate');
        Route::get('/members/qrcode/{id}', [\App\Http\Controllers\Admin\QRCodeController::class, 'generateQRCode1']);
        Route::get('/member-card/download/{id}', [\App\Http\Controllers\Admin\DataMembersController::class, 'downloadMemberCard'])->name('admin.card.download');

        //arsip


        Route::get('/archives/inbox', [\App\Http\Controllers\Admin\ArchiveController::class, 'inbox'])->name('admin.archives.inbox');
        Route::get('/archives/inbox/{id}/show', [\App\Http\Controllers\Admin\ArchiveController::class, 'showInbox'])->name('admin.archives.inbox.show');
        Route::post('/archives/inbox/{id}/update', [\App\Http\Controllers\Admin\ArchiveController::class, 'updateInbox'])->name('admin.archives.inbox.update');
        Route::post('/archives/disposition/inbox/{id}', [\App\Http\Controllers\Admin\ArchiveController::class, 'dispoInbox'])->name('admin.archives.inbox.disposition');
        Route::post('/archives/disposition/{id}', [\App\Http\Controllers\Admin\ArchiveController::class, 'dispo'])->name('admin.archives.disposition');
        Route::get('/archives/{id}/editarsip', [\App\Http\Controllers\Admin\ArchiveController::class, 'editArchive'])->name('admin.archives.editArchive');

        // For the index page (list all archives)
        Route::get('/archives', [\App\Http\Controllers\Admin\ArchiveController::class, 'index'])->name('admin.archives.index');
        // For showing the form to create a new archive
        Route::get('/archives/create', [\App\Http\Controllers\Admin\ArchiveController::class, 'create'])->name('admin.archives.create');
        // For storing a newly created archive
        Route::post('/archives', [\App\Http\Controllers\Admin\ArchiveController::class, 'store'])->name('admin.archives.store');
        // For displaying a specific archive
        Route::get('/archives/{archive}', [\App\Http\Controllers\Admin\ArchiveController::class, 'show'])->name('admin.archives.show');
        // For showing the form to edit an existing archive
        Route::get('/archives/{archive}/edit', [\App\Http\Controllers\Admin\ArchiveController::class, 'edit'])->name('admin.archives.edit');
        // For updating an existing archive
        Route::put('/archives/{archive}', [\App\Http\Controllers\Admin\ArchiveController::class, 'update'])->name('admin.archives.update');
        // Or, if you prefer PATCH for partial updates:
        // Route::patch('/archives/{archive}', [\App\Http\Controllers\Admin\ArchiveController::class, 'update'])->name('admin.archives.update');
        // For deleting an archive
        Route::delete('/archives/{archive}', [\App\Http\Controllers\Admin\ArchiveController::class, 'destroy'])->name('admin.archives.destroy');
        // Route::resource('/archives', \App\Http\Controllers\Admin\ArchiveController::class, ['as' => 'admin']);

        //jurnal
        Route::middleware('role:administrator,pendanaan')->group(function () {
            Route::get('/jurnals/export', [\App\Http\Controllers\Admin\JurnalController::class, 'exportReport'])->name('admin.jurnals.export');
            Route::get('/jurnals/show', [\App\Http\Controllers\Admin\JurnalController::class, 'show'])->name('admin.jurnals.show');
            Route::resource('/jurnals', \App\Http\Controllers\Admin\JurnalController::class, ['as' => 'admin']);
        });

        //laporan transaksi midtrans
        Route::middleware('role:administrator,pendanaan')->group(function () {
            Route::get('/midtrans-report', [\App\Http\Controllers\Admin\MidtransReportController::class, 'index'])->name('admin.midtrans-report.index');
            Route::get('/midtrans-report/export', [\App\Http\Controllers\Admin\MidtransReportController::class, 'export'])->name('admin.midtrans-report.export');
        });
        Route::get('/update-gender', [\App\Http\Controllers\Admin\DataMembersController::class, 'updateMissingGenders'])->name('missing.gender');



        Route::middleware('role:administrator')->group(function () {
            Route::post('/questions/{id}/import', [\App\Http\Controllers\Admin\QuestionsController::class, 'storeImport'])->name('admin.questions.import.store');
            Route::get('/questions/import', [\App\Http\Controllers\Admin\QuestionsController::class, 'import'])->name('admin.questions.import');

            //soal
            Route::resource('/questions', \App\Http\Controllers\Admin\QuestionsController::class, ['as' => 'admin']);

            //kelompok soal (bank soal)
            Route::resource('/question-categories', \App\Http\Controllers\Admin\QuestionCategoriesController::class, ['as' => 'admin']);
        });

    });
});



Route::get('/user/login', function () {

    //cek session users
    if(auth()->guard('member')->check()) {
        return redirect()->route('user.dashboard');
    }

    //return view login
    return \Inertia\Inertia::render('User/Auth/Login');
});

//login users
Route::post('/user/login', \App\Http\Controllers\User\LoginController::class)->middleware('throttle:6,1')->name('user.login');

//prefix "admin"
Route::prefix('user')->group(function() {

    //middleware "auth"
    Route::group(['middleware' => ['member']], function () {
        //route dashboard
        Route::get('/dashboard', App\Http\Controllers\User\DashboardController::class)->name('user.dashboard');
        Route::get('/notifications', [\App\Http\Controllers\User\NotificationController::class, 'index'])->name('user.notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\User\NotificationController::class, 'read'])->name('user.notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\User\NotificationController::class, 'readAll'])->name('user.notifications.read-all');
        Route::get('/profile', [\App\Http\Controllers\User\DataProfileController::class, 'index'])->name('user.profile');
        Route::get('/profile/data-utama', [\App\Http\Controllers\User\DataProfileController::class, 'indexIndiv'])->name('user.profile.data-utama');
        Route::post('/profile/image', [\App\Http\Controllers\User\DataProfileController::class, 'updateImage'])->name('user.profile.data-utama.image');
        Route::get('/profile/data-jabatan', [\App\Http\Controllers\User\DataProfileController::class, 'indexPosition'])->name('user.profile.jabatan');
        Route::get('/profile/data-lainnya', [\App\Http\Controllers\User\DataProfileController::class, 'indexOther'])->name('user.profile.lainnya');
        Route::get('/main-data', [\App\Http\Controllers\User\DataProfileController::class, 'indexIndiv'])->name('user.main-data');
        Route::get('/profile/edit', [\App\Http\Controllers\User\DataProfileController::class, 'edit'])->name('user.profile.edit');
        Route::post('/profile/edit', [\App\Http\Controllers\User\DataProfileController::class, 'update'])->name('user.profile.update');
        Route::get('/profile/data-jabatan/edit', [\App\Http\Controllers\User\DataProfileController::class, 'editPosition'])->name('user.profile.editposition');
        Route::post('/profile/data-jabatan/edit', [\App\Http\Controllers\User\DataProfileController::class, 'updatePosition'])->name('user.profile.updateposition');
        Route::get('/posts/list', [\App\Http\Controllers\User\PostsController::class, 'list'])->name('user.posts.list');
        Route::get('/posts/list/{post:slug}', [\App\Http\Controllers\User\PostsController::class, 'showlist'])->name('user.posts.showlist');
        Route::post('/posts/{id}', [\App\Http\Controllers\User\PostsController::class, 'update'])->name('user.posts.update');
        // show/edit are bound by slug (the frontend builds these URLs from
        // post.slug), so they're excluded from the resource() below and
        // declared explicitly. destroy/update use a plain $id and stay on
        // the resource - binding {post} globally would hand them a Post
        // object instead of a scalar id.
        Route::get('/posts/{post:slug}', [\App\Http\Controllers\User\PostsController::class, 'show'])->name('user.posts.show');
        Route::get('/posts/{post:slug}/edit', [\App\Http\Controllers\User\PostsController::class, 'edit'])->name('user.posts.edit');
        Route::resource('/posts', \App\Http\Controllers\User\PostsController::class, ['as' => 'user'])->except(['show', 'edit']);
        Route::post('/posts/{id}/submission', [\App\Http\Controllers\User\PostsController::class, 'submission'])->name('user.posts.submission');
        Route::resource('/events', \App\Http\Controllers\User\EventController::class, ['as' => 'user']);
        Route::get('/events/{event:slug}', [\App\Http\Controllers\User\EventController::class, 'show'])->name('user.events.join');
        Route::post('/events/{id}/join', [\App\Http\Controllers\User\EventController::class, 'join'])->name('user.events.join');
        Route::post('/events/{id}/absen', [\App\Http\Controllers\User\EventController::class, 'absen'])->name('user.events.absen');
        Route::get('/events/{event:slug}/info', [\App\Http\Controllers\User\EventController::class, 'info'])->name('user.events.info');
        Route::get('/setting', [App\Http\Controllers\User\LoginController::class, 'setting'])->name('user.setting');
        Route::put('/setting/update', [App\Http\Controllers\User\LoginController::class, 'resetPassword'])->name('user.setting.update');
        Route::get('/merchans/', [\App\Http\Controllers\User\MerchansController::class, 'index'])->name('user.merchan.index');
        Route::get('/merchans/{id}', [\App\Http\Controllers\User\MerchansController::class, 'show'])->name('user.merchan.show');
        Route::get('/member-card/', [\App\Http\Controllers\User\MemberCardController::class, 'index'])->name('user.card.index');
        Route::get('/member-card/edit', [\App\Http\Controllers\User\MemberCardController::class, 'edit'])->name('user.card.edit');
        Route::get('/member-card/download', [\App\Http\Controllers\User\MemberCardController::class, 'download'])->name('user.card.download');
        Route::get('/member-card/backcard', [\App\Http\Controllers\User\MemberCardController::class, 'downloadBackcard'])->name('user.card.back');
        Route::post('/member-card/save-member-card', [\App\Http\Controllers\User\MemberCardController::class, 'saveMemberCard'])->name('user.card.save');
        Route::post('/member-card/update', [\App\Http\Controllers\User\MemberCardController::class, 'updateImage'])->name('user.card.update');
        Route::get('/member-card/qrcode', [\App\Http\Controllers\User\MemberCardController::class, 'generateQRCode'])->name('user.card.qr');
        Route::get('/certificates', [\App\Http\Controllers\User\EventController::class, 'certificatesIndex'])->name('user.certificates.index');
        Route::get('/certificates/{id}', [\App\Http\Controllers\User\EventController::class, 'certificateView'])->name('admin.events.certificates.view');
         Route::get('/jurnals/show', [\App\Http\Controllers\User\JurnalController::class, 'show'])->name('user.jurnals.show');
        Route::get('/points', [\App\Http\Controllers\User\PointController::class, 'index'])->name('user.points.index');

        //route exam confirmation
        Route::get('/tryouts', [App\Http\Controllers\User\TryoutController::class, 'index'])->name('user.tryouts.index');
            //generate soal
        Route::post('/tryout/{id}/generate', [App\Http\Controllers\User\TryoutController::class, 'EnrollQuestion'])->name('user.tryouts.enrollQuestion');
         //route exam confirmation
        Route::get('/tryout-confirmation/{id}', [App\Http\Controllers\User\TryoutController::class, 'confirmation'])->name('user.tryouts.confirmation');
        //route exam start
        Route::get('/tryout-start/{id}', [App\Http\Controllers\User\TryoutController::class, 'startExam'])->name('student.exams.startExam');

         //route exam show
         Route::get('/tryout/{id}/{page}', [App\Http\Controllers\User\TryoutController::class, 'show'])->name('user.tryouts.show');
         //route exam update duration
        Route::put('/tryout-duration/update/{detail_event_id}', [App\Http\Controllers\User\TryoutController::class, 'updateDuration'])->name('user.tryouts.update_duration');

            //route answer question
        Route::post('/tryout-answer', [App\Http\Controllers\User\TryoutController::class, 'answerQuestion'])->name('user.tryouts.answerQuestion');
            //route exam end
        Route::post('/tryout-end', [App\Http\Controllers\User\TryoutController::class, 'endExam'])->name('user.tryouts.endExam');

         //route exam result
        Route::get('/tryout-result/{detail_event_id}', [App\Http\Controllers\User\TryoutController::class, 'resultExam'])->name('user.tryouts.resultExam');

    });
});


//public
Route::get('/registration', [\App\Http\Controllers\Public\RegistrationController::class, 'create'])->name('registration');
Route::get('/registration/berhasil', [\App\Http\Controllers\Public\RegistrationController::class, 'berhasil'])->name('registration.berhasil');
Route::post('/registration/store', [\App\Http\Controllers\Public\RegistrationController::class, 'store'])->middleware('throttle:6,1')->name('registration.store');
Route::get('/registration/success', [\App\Http\Controllers\Public\RegistrationController::class, 'index'])->name('registration.success');
Route::get('/registration/paid/{id}', [\App\Http\Controllers\Public\RegistrationController::class, 'show'])->name('registration.paid.show');
Route::get('/registration/paid/{id}/payment', [\App\Http\Controllers\Public\RegistrationController::class, 'payment'])->name('registration.paid.payment');
Route::post('/midtrans/notification', \App\Http\Controllers\Public\MidtransNotificationController::class)->name('midtrans.notification');
Route::get('/registration/confirm/{id}/edit', [\App\Http\Controllers\Public\RegistrationController::class, 'edit'])->name('registration.confirm.edit');
Route::post('/registration/confirm/{id}', [\App\Http\Controllers\Public\RegistrationController::class, 'update'])->name('registration.confirm.update');
Route::post('/registration/paid/{id}', [\App\Http\Controllers\Public\RegistrationController::class, 'paid'])->name('registration.paid.success');
// Route::get('/registration/confirm/{id}/success', [\App\Http\Controllers\Public\RegistrationController::class, 'update'])->name('registration.confirm.success');
Route::get('/registration/success', [\App\Http\Controllers\Public\RegistrationController::class, 'index'])->name('registration.success');
Route::get('/registration/group', [\App\Http\Controllers\Public\RegistrationController::class, 'group'])->name('registration.group');
Route::post('/registration/group', [\App\Http\Controllers\Public\RegistrationController::class, 'groupStore'])->middleware('throttle:6,1')->name('registration.group.store');
Route::get('/tentang-aspro', [\App\Http\Controllers\Public\PublicController::class, 'about'])->name('about');
Route::get('/ketua-umum', [\App\Http\Controllers\Public\PublicController::class, 'ketuaUmum'])->name('ketuaUmum');
Route::get('/peraturan-organisasi', [\App\Http\Controllers\Public\PublicController::class, 'peraturanOrganisasi'])->name('peraturanOrganisasi');
Route::get('/sejarah', [\App\Http\Controllers\Public\PublicController::class, 'sejarah'])->name('sejarah');
Route::get('/struktur-organisasi', [\App\Http\Controllers\Public\PublicController::class, 'strukturOrganisasi'])->name('strukturOrganisasi');
Route::get('/visi-misi', [\App\Http\Controllers\Public\PublicController::class, 'visiMisi'])->name('visiMisi');
Route::get('/bidang-hubungan-masyarakat-dan-kerja-sama', [\App\Http\Controllers\Public\PublicController::class, 'hubunganMasyarakat'])->name('hubunganMasyarakat');
Route::get('/bidang-hukum-dan-advokasi', [\App\Http\Controllers\Public\PublicController::class, 'hukumAdvokasi'])->name('hukumAdvokasi');
Route::get('/bidang-keanggotaan-dan-organisasi', [\App\Http\Controllers\Public\PublicController::class, 'keanggotaan'])->name('keanggotaan');
Route::get('/bidang-pengembangan-kapasitas-insani', [\App\Http\Controllers\Public\PublicController::class, 'pengembangan'])->name('pengembangan');
Route::get('/bidang-sumber-pendanaan-organisasi', [\App\Http\Controllers\Public\PublicController::class, 'sumberPendanaan'])->name('sumberPendanaan');
Route::get('/kontak-kami', [\App\Http\Controllers\Public\PublicController::class, 'kontak'])->name('kontak');
Route::get('/berita', [\App\Http\Controllers\Public\PublicController::class, 'berita'])->name('berita');
Route::get('/berita/{post:slug}', [\App\Http\Controllers\Public\PublicController::class, 'beritaView'])->name('berita.view');
Route::get('/data-anggota', [\App\Http\Controllers\Public\PublicController::class, 'dataAnggota'])->name('data-anggota');
Route::get('/data-anggota/chart', [\App\Http\Controllers\Public\PublicController::class, 'dataAnggotaChart'])->name('data-anggota.chart');
Route::get('/faq', [\App\Http\Controllers\Public\PublicController::class, 'faq'])->name('faq');
Route::get('/events/{slug}/certificate', [\App\Http\Controllers\Public\PublicController::class, 'indexCertificate'])->name('public.events.index-certificate');
// Route untuk menampilkan halaman form absen (Public/Website)
Route::get('/events/{slug}/absen', [\App\Http\Controllers\Public\PublicController::class, 'indexAbsen'])->name('public.events.absen');
// Route untuk memproses absensi
Route::post('/events/{id}/absen', [\App\Http\Controllers\Public\PublicController::class, 'absen'])->name('public.events.absen.store');
Route::get('/events/{event:slug}', [\App\Http\Controllers\Public\EventsController::class, 'show'])->name('event.show');
Route::get('/events/{event:slug}/dashboard', [\App\Http\Controllers\Public\PublicController::class, 'eventDashboard'])->name('event.dashboard');
Route::resource('/events', \App\Http\Controllers\Public\EventsController::class);
Route::resource('/berita', \App\Http\Controllers\Public\PostsController::class);
Route::get('/sdma-menulis', [\App\Http\Controllers\Public\PostsController::class, 'articles'])->name('artikel');
Route::get('/sdma-menulis/{post:slug}', [\App\Http\Controllers\Public\PostsController::class, 'show'])->name('show.artikel');
Route::get('/certificates/search', [\App\Http\Controllers\Public\PublicController::class, 'certificateSearch'])->name('certificateSearch');
Route::get('/certificates/filter', [\App\Http\Controllers\Public\PublicController::class, 'certificateFilter'])->name('certificateFilter');
Route::resource('/merchans', \App\Http\Controllers\Public\MerchansController::class);
Route::get('/forget-password', [\App\Http\Controllers\Public\PublicController::class, 'forgetPassword'])->name('forget.password');
Route::post('/forget-password/email', [\App\Http\Controllers\Public\PublicController::class, 'emailforgetPassword'])->middleware('throttle:6,1')->name('forget.password.email');
Route::get('/user/forget-password/{id}', [\App\Http\Controllers\Public\PublicController::class, 'IndexforgetPassword'])->name('forget.password.index');
Route::put('/user/forget-password/{id}/reset', [\App\Http\Controllers\Public\PublicController::class, 'ResetPassword'])->middleware('throttle:6,1')->name('forget.password.reset');
Route::get('/identity-verification/{member:qr_link}', [\App\Http\Controllers\Public\PublicController::class, 'profileView'])->name('profile.view');
Route::get('/identity-verification/{member:qr_link}/{event}/download', [\App\Http\Controllers\Public\PublicController::class, 'downloadSertifikat'])->name('profile.sertifikat.download');
Route::get('/verification/{id}', [\App\Http\Controllers\Public\PublicController::class, 'documentVerif'])->name('documentVerif');
Route::post('/hubungi-aspro', [\App\Http\Controllers\Public\ArchiveController::class, 'store'])->middleware('throttle:6,1')->name('hubungi-aspro.store');
Route::resource('/hubungi-aspro', \App\Http\Controllers\Public\ArchiveController::class)->except(['store']);
// Route::post('/hubungi-aspro/store', [\App\Http\Controllers\Public\ArchiveController::class, 'store'])->name('hubungi-aspro.store');
// Route::post('/hubungi-aspro/tiket', [\App\Http\Controllers\Public\ArchiveController::class, 'show'])->name('hubungi-aspro.show');
Route::get('/certificates/{id}/view', [\App\Http\Controllers\Public\PublicController::class, 'certificatesShow'])->name('certificate.show');
Route::get('/certificates/{id}', [\App\Http\Controllers\Public\PublicController::class, 'certificateView'])->name('certificate.view');
Route::get('/maintenance', [\App\Http\Controllers\Public\PublicController::class, 'maintenance'])->name('maintenance');


Route::get('/documents/{filename}', function ($filename) {
    $path = storage_path('app/public/document/' . $filename);

    if (!file_exists($path)) {//
        abort(404);
    }

    return response()->file($path);
})->name('documents.show');
