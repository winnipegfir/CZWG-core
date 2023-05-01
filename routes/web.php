<?php

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

//ALL Public Views
Route::get('/', 'HomeController@view')->name('index');
Route::view('/airports', 'airports')->name('airports');
Route::get('/nate', 'HomeController@nate')->name('nate');
Route::get('/roster', 'AtcTraining\RosterController@showPublic')->name('roster.public');
Route::get('/roster/{id}', 'Users\UserController@viewProfile');
Route::get('/roster/{id}/connections', 'Users\UserController@viewConnections');
Route::get('/join', 'AtcTraining\ApplicationsController@joinWinnipeg')->name('join.public');
Route::get('/staff', 'Users\StaffListController@index')->name('staff');
Route::get('/policies', 'Publications\PoliciesController@index')->name('policies');
Route::get('/meetingminutes', 'News\NewsController@minutesIndex')->name('meetingminutes');
Route::view('/privacy', 'privacy')->name('privacy');
Route::get('/yourfeedback', 'Feedback\FeedbackController@yourFeedback')->name('yourfeedback');
Route::get('/events', 'Events\EventController@index')->name('events.index');
Route::get('/events/{slug}', 'Events\EventController@viewEvent')->name('events.view');
Route::view('/about', 'about')->name('about');
Route::view('/branding', 'branding')->name('branding');
Route::get('/news/{slug}', 'News\NewsController@viewArticlePublic')->name('news.articlepublic');
Route::get('/news', 'News\NewsController@viewAllPublic')->name('news');
Route::get('/training', 'AtcTraining\TrainingController@trainingTime')->name('training');
Route::view('/bill', 'bill')->name('bill');
Route::view('/wpg', 'wpg')->name('wpg');
Route::view('/yearend2021', 'yearend2021')->name('yearend2021');
Route::view('/yearend2020', 'yearend2020')->name('yearend2020');
Route::view('/yearend', 'yearend')->name('yearend');
Route::view('/pdc', 'pdc')->name('pdc');
Route::view('/excds', 'excds')->name('excds');
Route::view('/vote', 'vote')->name('vote');

Route::prefix('instructors')->group(function () {
    Route::view('/', 'instructors')->name('instructors');
    Route::post('/', 'AtcTraining\TeachersController@store')->name('instructors.store')->middleware('staff');
    Route::get('{id}', 'AtcTraining\TeachersController@delete')->name('instructors.delete')->middleware('staff');
});

//Redirects
Route::get('/merch', function () {
    return redirect()->to('https://www.designbyhumans.com/shop/WinnipegFIR');
});

Route::get('/github', function () {
    return redirect()->to('https://github.com/winnipegfir/CZWG-core');
});

//Authentication

Route::get('/sso/login', 'Auth\LoginController@ssoLogin')->middleware('guest')->name('auth.sso.login');
Route::get('/sso/validate', 'Auth\LoginController@validateSsoLogin')->middleware('guest');
Route::get('/connect/login', 'Auth\LoginController@connectLogin')->middleware('guest')->name('auth.connect.login');
Route::get('/connect/validate', 'Auth\LoginController@validateConnectLogin')->middleware('guest');
Route::get('/logout', 'Auth\LoginController@logout')->middleware('auth')->name('auth.logout');

//Base level authentication
Route::group(['middleware' => 'auth'], function () {
    //Privacy accept
    Route::get('/privacyaccept', 'Users\UserController@privacyAccept');
    Route::get('/privacydeny', 'Users\UserController@privacyDeny');

    //Visiting/Join Applications
    Route::group(['middleware' => 'notcertified'], function () {
        Route::get('/dashboard/application', 'AtcTraining\ApplicationsController@startApplicationProcess')->name('application.start');
        Route::post('/dashboard/application', 'AtcTraining\ApplicationsController@submitApplication')->name('application.submit');
    });

    //User Tickets
    Route::get('/dashboard/tickets', 'Tickets\TicketsController@index')->name('tickets.index');
    Route::get('/dashboard/tickets/{id}', 'Tickets\TicketsController@viewTicket')->name('tickets.viewticket');
    Route::post('/dashboard/tickets', 'Tickets\TicketsController@startNewTicket')->name('tickets.startticket');
    Route::post('/dashboard/tickets/{id}', 'Tickets\TicketsController@addReplyToTicket')->name('tickets.reply');

    Route::group(['middleware' => ['role:Administrator|Staff']], function () {
        Route::prefix('admin')->group(function () {
            //Uploads
            Route::get('/upload', 'Publications\UploadController@upload')->middleware('staff')->name('dashboard.upload');
            Route::post('/upload', 'Publications\UploadController@uploadPost')->middleware('staff')->name('dashboard.upload.post');
            //View Feedback
            Route::get('/feedback', 'Feedback\FeedbackController@index')->name('staff.feedback.index');
            Route::get('/feedback/controller/{id}', 'Feedback\FeedbackController@viewControllerFeedback')->name('staff.feedback.controller');
            Route::post('/feedback/controller/{id}', 'Feedback\FeedbackController@editControllerFeedback')->name('staff.feedback.controller.edit');
            Route::get('/feedback/controller/{id}/approve', 'Feedback\FeedbackController@approveControllerFeedback');
            Route::get('/feedback/controller/{id}/deny', 'Feedback\FeedbackController@denyControllerFeedback');
            Route::get('/feedback/controller/{id}/delete', 'Feedback\FeedbackController@deleteControllerFeedback');
            Route::get('/feedback/website/{id}', 'Feedback\FeedbackController@viewWebsiteFeedback')->name('staff.feedback.website');
            Route::get('/feedback/website/{id}/delete', 'Feedback\FeedbackController@deleteWebsiteFeedback');
        });

        //Closing, re-opening, and placing tickets on hold
        Route::prefix('dashboard/tickets')->group(function () {
            Route::post('/{id}/close', 'Tickets\TicketsController@closeTicket')->name('tickets.closeticket');
            Route::get('/{id}/hold', 'Tickets\TicketsController@onholdTicket')->name('tickets.onholdticket');
            Route::get('/{id}/open', 'Tickets\TicketsController@openTicket')->name('tickets.openticket');
        });

        //View Tickets (staff)
        Route::get('/dashboard/staff/tickets', 'Tickets\TicketsController@staffIndex')->name('tickets.staff');

        //Staff News
        Route::prefix('admin/news')->group(function () {
            Route::get('/', 'News\NewsController@index')->name('news.index');
            Route::get('/article/create', 'News\NewsController@createArticle')->name('news.articles.create');
            Route::post('/article/create', 'News\NewsController@postArticle')->name('news.articles.create.post');
            Route::get('/article/{slug}', 'News\NewsController@viewArticle')->name('news.articles.view');
            Route::get('/article/delete/{id}', 'News\NewsController@deleteArticle')->name('news.articles.delete');
            Route::post('/article/edit/{id}', 'News\NewsController@editArticle')->name('news.articles.edit');
        });

        //Assigning Instructor
        Route::prefix('instructor')->group(function () {
            Route::post('/add', 'AtcTraining\TrainingController@assignStudent')->name('instructor.student.add');
            Route::post('/add', 'AtcTraining\TrainingController@newStudent')->name('instructor.student.add.new');
            Route::get('/delete/{id}', 'AtcTraining\TrainingController@deleteStudent')->name('instructor.student.delete');
        });
    });

    //User Event Applications
    Route::post('/dashboard/events/controllerapplications/ajax', 'Events\EventController@controllerApplicationAjaxSubmit')->name('events.controllerapplication.ajax');
    Route::get('/dashboard/events/view', 'Events\EventController@viewControllers');

    //Staff Events
    Route::group(['prefix' => 'admin/events', 'middleware' => ['role:Administrator|Staff']], function () {
        Route::get('/', 'Events\EventController@adminIndex')->name('events.admin.index');
        Route::get('/create', 'Events\EventController@adminCreateEvent')->middleware(['role:Administrator|Events-Coordinator'])->name('events.admin.create');
        Route::post('/create', 'Events\EventController@adminCreateEventPost')->middleware(['role:Administrator|Events-Coordinator'])->name('events.admin.create.post');
        Route::post('/{slug}/edit', 'Events\EventController@adminEditEventPost')->middleware(['role:Administrator|Events-Coordinator'])->name('events.admin.edit.post');
        Route::post('/{slug}/update/create', 'Events\EventController@adminCreateUpdatePost')->middleware(['role:Administrator|Events-Coordinator'])->name('events.admin.update.post');
        Route::get('/{slug}', 'Events\EventController@adminViewEvent')->name('events.admin.view');
        Route::get('/{slug}/delete', 'Events\EventController@adminDeleteEvent')->middleware(['role:Administrator|Events-Coordinator'])->name('events.admin.delete');
        Route::get('/{slug}/controllerapps/{cid}/delete', 'Events\EventController@adminDeleteControllerApp')->middleware(['role:Administrator|Chief-Instructor|Events-Coordinator'])->name('events.admin.controllerapps.delete');
        Route::get('/{slug}/updates/{id}/delete', 'Events\EventController@adminDeleteUpdate')->middleware(['role:Administrator|Events-Coordinator'])->name('events.admin.update.delete');
        Route::get('/applications/{id}', 'Events\EventController@viewApplications')->middleware(['role:Administrator|Chief-Instructor|Events-Coordinator'])->name('event.viewapplications');
        Route::post('/applications/confirm/{id}', 'Events\EventController@confirmController')->middleware(['role:Administrator|Chief-Instructor|Events-Coordinator'])->name('event.confirmapplication');
        Route::post('/applications/manualconfirm/{id}', 'Events\EventController@addController')->middleware(['role:Administrator|Chief-Instructor|Events-Coordinator'])->name('event.addcontroller');
        Route::post('/applications/manualconfirm/delete/{id}', 'Events\EventController@deleteController')->middleware(['role:Administrator|Chief-Instructor|Events-Coordinator'])->name('event.deletecontroller');
    });

    //Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', 'DashboardController@index')->name('dashboard.index');
        Route::get('/cbtdismiss/{id}', 'DashboardController@dismissCbtNotification')->name('cbt.notification.dismiss');
        Route::post('/users/changeavatar', 'Users\UserController@changeAvatar')->name('users.changeavatar');
        Route::get('/users/changeavatar/discord', 'Users\UserController@changeAvatarDiscord')->name('users.changeavatar.discord');
        Route::get('/users/resetavatar', 'Users\UserController@resetAvatar')->name('users.resetavatar');
        Route::post('/users/changedisplayname', 'Users\UserController@changeDisplayName')->name('users.changedisplayname');
        Route::get('/users/defaultavatar/{id}', function ($id) {
            $user = \App\User::whereId($id)->firstOrFail();
            if ($user->isAvatarDefault()) {
                return true;
            }

            return false;
        });

        //Roster
        Route::group(['middleware' => ['role:Administrator|Instructor']], function () {
            Route::get('/roster', 'AtcTraining\RosterController@index')->name('roster.index');
            Route::post('/roster/controller/add/', 'AtcTraining\RosterController@addController')->name('roster.addcontroller');
            Route::post('/roster/controller/addv/', 'AtcTraining\RosterController@addVisitController')->name('roster.addvisitcontroller');
            Route::post('/roster/edit/{id}', 'AtcTraining\RosterController@editController')->name('roster.editcontroller');
            Route::get('/roster/edit/{id}', 'AtcTraining\RosterController@editControllerForm')->name('roster.editcontrollerform');
            Route::get('/roster/{id}', 'AtcTraining\RosterController@viewController')->name('roster.viewcontroller');
            Route::get('/roster/{id}/delete/', 'AtcTraining\RosterController@deleteController')->name('roster.deletecontroller');
        });

        //Email prefs
        Route::get('/emailpref', 'Users\DataController@emailPref')->name('dashboard.emailpref');
        Route::get('/emailpref/subscribe', 'Users\DataController@subscribeEmails');
        Route::get('/emailpref/unsubscribe', 'Users\DataController@unsubscribeEmails');

        //Applications View/Accept/Deny
        Route::group(['middleware' => ['role:Administrator|Chief-Instructor']], function () {
            Route::get('/training/applications', 'AtcTraining\ApplicationsController@viewAllApplications')->name('training.applications');
            Route::get('/training/applications/{id}', 'AtcTraining\TrainingController@viewApplication')->name('training.viewapplication');
            Route::get('/training/applications/{id}/accept', 'AtcTraining\TrainingController@acceptApplication')->name('training.application.accept');
            Route::get('/training/applications/{id}/deny', 'AtcTraining\TrainingController@denyApplication')->name('training.application.deny');
            Route::post('/training/applications/{id}/', 'AtcTraining\TrainingController@editStaffComment')->name('training.application.savestaffcomment');
        });
    });
    // '/me'
    Route::prefix('dashboard/me')->group(function () {
        Route::post('/editbiography', 'Users\UserController@editBio')->name('me.editbio');
        Route::get('/discord/link', 'Users\UserController@linkDiscord')->name('me.discord.link');
        Route::get('/discord/unlink', 'Users\UserController@unlinkDiscord')->name('me.discord.unlink');
        Route::get('/discord/link/redirect', 'Users\UserController@linkDiscordRedirect')->name('me.discord.link.redirect');
        Route::get('/discord/server/join', 'Users\UserController@joinDiscordServerRedirect')->name('me.discord.join');
        Route::get('/discord/server/join/redirect', 'Users\UserController@joinDiscordServer');
        Route::get('/preferences', 'Users\UserController@preferences')->name('me.preferences');
        Route::post('/preferences', 'Users\UserController@preferencesPost')->name('me.preferences.post');
        //GDPR
        Route::get('/data', 'Users\DataController@index')->name('me.data');
        Route::post('/data/export/all', 'Users\DataController@exportAllData')->name('me.data.export.all');
    });

    //Users View/Edit
    Route::group(['prefix' => 'admin/users', 'middleware' => ['role:Administrator|Staff']], function () {
        Route::get('/', 'Users\UserController@viewAllUsers')->name('users.viewall');
        Route::post('/search/ajax', 'Users\UserController@searchUsers')->name('users.search.ajax');
        Route::get('{id}', 'Users\UserController@adminViewUserProfile')->name('users.viewprofile');
        Route::post('/{id}', 'Users\UserController@createUserNote')->name('users.createnote');
        Route::post('/edit/{id}', 'Users\UserController@editPermissions')->name('edit.userpermissions');
        Route::get('/{user_id}/note/{note_id}/delete', 'Users\UserController@deleteUserNote')->name('users.deletenote');
        Route::post('/func/avatarchange', 'Users\UserController@changeUsersAvatar')->name('users.changeusersavatar');
        Route::post('/func/avatarreset', 'Users\UserController@resetUsersAvatar')->name('users.resetusersavatar');
        Route::post('/func/bioreset', 'Users\UserController@resetUsersBio')->name('users.resetusersbio');
        Route::get('/{id}/delete', 'Users\UserController@deleteUser');
        Route::get('/{id}/edit', 'Users\UserController@editUser')->name('users.edit.create');
        Route::post('/{id}/edit', 'Users\UserController@storeEditUser')->name('users.edit.store');
        Route::post('/{id}/bookingban/create', 'Users\UserController@createBookingBan')->name('users.bookingban.create');
        Route::post('/{id}/bookingban/remove', 'Users\UserController@removeBookingBan')->name('users.bookingban.remove');
        Route::get('/{id}/email', 'Users\UserController@emailCreate')->name('users.email.create');
        Route::get('/{id}/email', 'Users\UserController@emailStore')->name('users.email.store');
    });

    //Feedback
    Route::get('/feedback', 'Feedback\FeedbackController@create')->name('feedback.create');
    Route::post('/feedback', 'Feedback\FeedbackController@createPost')->name('feedback.create.post');

    //ATC Resources View
    Route::get('/atcresources', 'Publications\AtcResourcesController@index')->middleware(['role:Controller'])->name('atcresources.index');

    //Upload and Delete ATC Resources
    Route::group(['middleware' => ['role:Administrator|Staff']], function () {
        Route::post('/atcresources', 'Publications\AtcResourcesController@uploadResource')->name('atcresources.upload');
        Route::get('/atcresources/delete/{id}', 'Publications\AtcResourcesController@deleteResource')->name('atcresources.delete');
    });

    //View CBT
    Route::prefix('dashboard/training/cbt')->group(function () {
        Route::get('/', 'AtcTraining\CBTController@index')->name('cbt.index');
        Route::get('/module', 'AtcTraining\CBTController@moduleindex')->name('cbt.module');
        Route::get('/module/view/{id}/{progress}', 'AtcTraining\CBTController@viewmodule')->name('cbt.module.view');
        Route::get('/module/finish/{id}', 'AtcTraining\CBTController@completeModule')->name('cbt.module.complete');
        Route::get('/exam', 'AtcTraining\CBTController@examindex')->name('cbt.exam');
        Route::get('/exam/start/{id}', 'AtcTraining\CBTController@startExam')->name('cbt.exam.begin');
        Route::get('/exam/{id}', 'AtcTraining\CBTController@exam')->name('cbt.exam.start');
        Route::post('exam/grade/{id}', 'AtcTraining\CBTController@gradeExam')->name('cbt.exam.grade');
        Route::get('exam/results/{id}/{sid}', 'AtcTraining\CBTController@examResults')->name('cbt.exam.results');
        //Mentor
        Route::group(['middleware' => ['role:Administrator|Instructor|Mentor']], function () {
            Route::get('/moduleadmin', 'AtcTraining\CBTController@moduleindexadmin')->name('cbt.module.admin');
        });
        //Instructor
        Route::group(['middleware' => ['role:Administrator|Instructor']], function () {
            Route::post('/exam/assign', 'AtcTraining\TrainingController@assignExam')->name('cbt.exam.assign');
            Route::post('/module/assign', 'AtcTraining\TrainingController@assignModule')->name('cbt.module.assign');
            Route::get('/module/unassign/{id}', 'AtcTraining\TrainingController@ModuleUnassign')->name('cbt.module.unassign');
            Route::get('/examadmin', 'AtcTraining\CBTController@examadminview')->name('cbt.exam.adminview');
            Route::get('/examadmin/view/{id}', 'AtcTraining\CBTController@questionBank')->name('cbt.exam.questions');
            Route::get('/examadmin/unassign/{id}', 'AtcTraining\TrainingController@unassignExam')->name('cbt.exam.unassign');
            Route::get('/module/edit/{id}', 'AtcTraining\CBTController@editModule')->name('cbt.module.edit');
        });
        //Staff/Admin
        Route::group(['middleware' => ['role:Administrator|Chief-Instructor']], function () {
            Route::post('/addexam', 'AtcTraining\CBTController@addExam')->name('cbt.exam.add');
            Route::get('/deleteexam/{id}', 'AtcTraining\CBTController@deleteExam')->name('cbt.exam.delete');
            Route::post('/module/add', 'AtcTraining\CBTController@addModule')->name('cbt.module.add');
            Route::post('/module/editdetails/{id}', 'AtcTraining\CBTController@editModuleDetails')->name('cbt.edit.moduledetails');
            Route::get('/module/assignall/{id}', 'AtcTraining\CBTController@assignModuleAll')->name('cbt.module.assignall');
            Route::get('/module/unassignall/{id}', 'AtcTraining\CBTController@moduleUnassignall')->name('cbt.module.unassignall');
            Route::get('/module/admin/{id}', 'AtcTraining\CBTController@viewAdminModule')->name('cbt.module.view.admin');
            Route::get('/module/delete/{id}', 'AtcTraining\CBTController@deleteModule')->name('cbt.module.delete');
            Route::post('/lesson/add/{id}', 'AtcTraining\CBTController@addLesson')->name('cbt.lesson.add');
            Route::get('/lesson/e/{id}', 'AtcTraining\CBTController@editLesson')->name('cbt.lesson.edit');
            Route::post('/lesson/edit/{id}', 'AtcTraining\CBTController@processEditLesson')->name('cbt.lesson.submit');
            Route::get('/lesson/delete/{id}', 'AtcTraining\CBTController@deleteLesson')->name('cbt.lesson.delete');
            Route::post('/examadmin/add/{id}', 'AtcTraining\CBTController@addQuestion')->name('cbt.exam.question.add');
            Route::post('/examadmin/update/{id}', 'AtcTraining\CBTController@updateQuestion')->name('cbt.exam.question.update');
            Route::get('/examadmin/delete/{id}', 'AtcTraining\CBTController@deleteQuestion')->name('cbt.exam.question.delete');
        });
    });

    //ADMIN ONLY
    //Minutes
    Route::group(['middleware' => ['role:Administrator']], function () {
        Route::get('/meetingminutes/{id}', 'News\NewsController@minutesDelete')->name('meetingminutes.delete');
        Route::post('/meetingminutes', 'News\NewsController@minutesUpload')->name('meetingminutes.upload');
        //Network
        Route::get('/admin/network', 'Network\NetworkController@index')->name('network.index');
        Route::get('/admin/network/monitoredpositions', 'Network\NetworkController@monitoredPositionsIndex')->name('network.monitoredpositions.index');
        Route::get('/admin/network/monitoredpositions/{position}', 'Network\NetworkController@viewMonitoredPosition')->name('network.monitoredpositions.view');
        Route::post('/admin/network/monitoredpositions/create', 'Network\NetworkController@createMonitoredPosition')->name('network.monitoredpositions.create');
        //Policy creation and settings
        Route::post('/policies', 'Publications\PoliciesController@addPolicy')->name('policies.create');
        Route::post('/policies/{id}/edit', 'Publications\PoliciesController@editPolicy');
        Route::get('/policies/{id}/delete', 'Publications\PoliciesController@deletePolicy');
        Route::post('/policies/section/create', 'Publications\PoliciesController@addPolicySection')->name('policysection.create');
        Route::get('/policies/section/{id}/delete', 'Publications\PoliciesController@deletePolicySection');

        //Settings
        Route::prefix('admin/settings')->group(function () {
            Route::get('/', 'Settings\SettingsController@index')->name('settings.index');
            Route::get('/roles', 'Settings\SettingsController@viewRoles')->name('roles.view');
            Route::post('/addrole', 'Settings\SettingsController@addRole')->name('roles.add');
            Route::get('/deleterole/{id}', 'Settings\SettingsController@deleteRole')->name('roles.delete');
            Route::post('/userrole/add', 'Users\UserController@addRole')->name('user.role.add');
            Route::get('/deleteuserrole/{id}/{user}', 'Users\UserController@deleterole')->name('user.role.delete');
            Route::get('/site-information', 'Settings\SettingsController@siteInformation')->name('settings.siteinformation');
            Route::post('/site-information', 'Settings\SettingsController@saveSiteInformation')->name('settings.siteinformation.post');
            Route::get('/emails', 'Settings\SettingsController@emails')->name('settings.emails');
            Route::post('/emails', 'Settings\SettingsController@saveEmails')->name('settings.emails.post');
            Route::get('/audit-log', 'Settings\SettingsController@auditLog')->name('settings.auditlog');
            Route::get('/staff', 'Users\StaffListController@editIndex')->name('settings.staff');
            Route::post('/staff/{id}', 'Users\StaffListController@editStaffMember')->name('settings.staff.editmember');
            Route::post('/staff/a/add', 'Users\StaffListController@addStaffMember')->name('settings.staff.addmember');
            Route::post('/staff/{id}/delete', 'Users\StaffListController@deleteStaffMember')->name('settings.staff.deletemember');
            Route::get('/banner', 'Settings\SettingsController@banner')->name('settings.banner');
            Route::post('/banner', 'Settings\SettingsController@bannerEdit')->name('settings.banner.edit');
            Route::get('/images', 'Settings\SettingsController@imagesIndex')->name('settings.images');
            Route::post('images', 'Settings\SettingsController@uploadImage')->name('settings.images.upload');
            Route::post('/images/edit/{id}', 'Settings\SettingsController@editImage')->name('settings.images.edit');
            Route::get('/images/test/{id}', 'Settings\SettingsController@testImage')->name('settings.images.test');
            Route::get('/images/delete/{id}', 'Settings\SettingsController@deleteImage')->name('settings.images.delete');
        });
    });
});

//NOT BEING USED CURRENTLY
//Bookings
//Route::group(['middleware' => 'certified'], function () {
//Route::get('/dashboard/bookings', 'ControllerBookings\ControllerBookingsController@index')->name('controllerbookings.index');
//Route::get('/dashboard/bookings/create', 'ControllerBookings\ControllerBookingsController@create')->name('controllerbookings.create');
//Route::post('/dashboard/bookings/create', 'ControllerBookings\ControllerBookingsController@createPost')->name('controllerbookings.create.post');
//  });

//AtcTraining
Route::get('/dashboard/training', 'AtcTraining\TrainingController@index')->name('training.index');
Route::post('/training', 'AtcTraining\TrainingController@editTrainingTime')->middleware('staff')->name('waittime.edit');
Route::group(['middleware' => ['role:Administrator|Instructor']], function () {
    Route::get('/dashboard/training/sessions', 'AtcTraining\TrainingController@instructingSessionsIndex')->name('training.instructingsessions.index');
    Route::get('/dashboard/training/sessions/{id}', 'AtcTraining\TrainingController@viewInstructingSession')->name('training.instructingsessions.viewsession');
    Route::view('/dashboard/training/sessions/create', 'dashboard.training.instructingsessions.create')->name('training.instructingsessions.createsessionindex');
    Route::get('/dashboard/training/sessions/create', 'AtcTraining\TrainingController@createInstructingSession')->name('training.instructingsessions.createsession');
    Route::get('/dashboard/training/instructors', 'AtcTraining\TrainingController@instructorsIndex')->name('training.instructors');
    Route::get('/dashboard/training/students/current', 'AtcTraining\TrainingController@currentStudents')->name('training.students.current');
    Route::get('/dashboard/training/students/new', 'AtcTraining\TrainingController@newStudents')->name('training.students.new');
    Route::get('/dashboard/training/students/completed', 'AtcTraining\TrainingController@completedStudents')->name('training.students.completed');
    Route::get('/dashboard/training/students/waitlist', 'AtcTraining\TrainingController@newStudents')->name('training.students.waitlist');
    Route::get('/dashboard/training/students/{id}', 'AtcTraining\TrainingController@viewStudent')->name('training.students.view');
    Route::post('/dashboard/training/students/{id}/assigninstructor', 'AtcTraining\TrainingController@assignInstructorToStudent')->name('training.students.assigninstructor');
    Route::post('/dashboard/training/students/{id}/setstatus', 'AtcTraining\TrainingController@changeStudentStatus')->name('training.students.setstatus');
    Route::get('/dashboard/trainingnotes/{id}', 'AtcTraining\TrainingController@viewNote')->name('trainingnote.view');
    //  Route::get('/dashboard/trainingnotes/{id}/delete', 'AtcTraining\TrainingNotesController@delete')->name('trainingnotes.delete');
    Route::post('/dashboard/trainingnotes/add/{id}', 'AtcTraining\TrainingController@addNote')->name('add.trainingnote');
    Route::get('/dashboard/trainingnotes/create/{id}', 'AtcTraining\TrainingController@newNoteView')->name('view.add.note');
    Route::post('/training/solorequest/{id}', 'AtcTraining\TrainingController@soloRequest')->name('training.solo.request');

    //AtcTraining
    Route::post('/dashboard/training/instructors', 'AtcTraining\TrainingController@addInstructor')->name('training.instructors.add');
});
//Admin and CI
Route::group(['middleware' => ['role:Administrator|Chief-Instructor']], function () {
    Route::get('/training/solo/approve/{id}', 'AtcTraining\TrainingController@approveSoloRequest')->name('training.solo.approve');
    Route::get('/training/solo/deny/{id}', 'AtcTraining\TrainingController@denySoloRequest')->name('training.solo.deny');
});
