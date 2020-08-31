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
Route::get('/map', 'HomeController@map')->name('map');
Route::get('/airports', 'HomeController@airports')->name('airports');
Route::get('/nate', 'HomeController@nate')->name('nate');
Route::get('/roster', 'AtcTraining\RosterController@showPublic')->name('roster.public');
Route::get('/roster/{id}', 'Users\UserController@viewProfile');
Route::get('/roster/{id}/connections', 'Users\UserController@viewConnections');
Route::get('/join', 'AtcTraining\ApplicationsController@joinWinnipeg')->name('join.public');
Route::get('/staff', 'Users\StaffListController@index')->name('staff');
Route::view('/pilots/tutorial', 'pilots.tutorial');
Route::get('/policies', 'Publications\PoliciesController@index')->name('policies');
Route::get('/meetingminutes', 'News\NewsController@minutesIndex')->name('meetingminutes');
Route::get('/bookings', 'ControllerBookings\ControllerBookingsController@indexPublic')->name('controllerbookings.public');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/changelog', 'changelog')->name('changelog');
Route::view('/emailtest', 'emails.announcement');
Route::get('/events', 'Events\EventController@index')->name('events.index');
Route::get('/events/{slug}', 'Events\EventController@viewEvent')->name('events.view');
Route::view('/about', 'about')->name('about');
Route::view('/branding', 'branding')->name('branding');
Route::get('/news/{id}', 'News\NewsController@viewArticlePublic')->name('news.articlepublic')->where('id', '[0-9]+');
Route::get('/news/{slug}', 'News\NewsController@viewArticlePublic')->name('news.articlepublic');
Route::get('/news', 'News\NewsController@viewAllPublic')->name('news');
Route::post('/instructor/add', 'AtcTraining\TrainingController@assignStudent')->middleware('staff')->name('instructor.student.add');
Route::post('/instructor/add', 'AtcTraining\TrainingController@newStudent')->middleware('staff')->name('instructor.student.add.new');
Route::get('/instructor/delete/{id}', 'AtcTraining\TrainingController@deleteStudent')->middleware('staff')->name('instructor.student.delete');


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

    Route::group(['middleware' => 'staff'], function () {
        Route::prefix('admin')->group(function () {
            //Image Uploads
            Route::get('/imageupload', 'Publications\ImageUploadController@imageUpload')->middleware('staff')->name('dashboard.image');
            Route::post('/imageupload', 'Publications\ImageUploadController@imageUploadPost')->middleware('staff')->name('dashboard.image.upload');
            //View Feedback
            Route::get('/feedback', 'Feedback\FeedbackController@index')->name('staff.feedback.index');
            Route::get('/feedback/controller/{id}', 'Feedback\FeedbackController@viewControllerFeedback')->name('staff.feedback.controller');
            Route::get('/feedback/website/{id}', 'Feedback\FeedbackController@viewWebsiteFeedback')->name('staff.feedback.website');

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

    //Staff Events
    Route::group(['prefix' => 'admin/events', 'middleware' => 'staff'], function () {
        Route::get('/', 'Events\EventController@adminIndex')->name('events.admin.index');
        Route::get('/create', 'Events\EventController@adminCreateEvent')->name('events.admin.create');
        Route::post('/create', 'Events\EventController@adminCreateEventPost')->name('events.admin.create.post');
        Route::post('/{slug}/edit', 'Events\EventController@adminEditEventPost')->name('events.admin.edit.post');
        Route::post('/{slug}/update/create', 'Events\EventController@adminCreateUpdatePost')->name('events.admin.update.post');
        Route::get('/{slug}', 'Events\EventController@adminViewEvent')->name('events.admin.view');
        Route::get('/{slug}/delete', 'Events\EventController@adminDeleteEvent')->name('events.admin.delete');
        Route::get('/{slug}/controllerapps/{cid}/delete', 'Events\EventController@adminDeleteControllerApp')->name('events.admin.controllerapps.delete');
        Route::get('/{slug}/updates/{id}/delete', 'Events\EventController@adminDeleteUpdate')->name('events.admin.update.delete');
        Route::get('/applications/{id}', 'Events\EventController@viewApplications')->name('event.viewapplications');
        Route::post('/applications/confirm/{id}', 'Events\EventController@confirmController')->name('event.confirmapplication');
        Route::post('/applications/manualconfirm/{id}', 'Events\EventController@addController')->name('event.addcontroller');
        Route::post('/applications/manualconfirm/delete/{id}', 'Events\EventController@deleteController')->name('event.deletecontroller');
    });

    //Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', 'DashboardController@index')->name('dashboard.index');
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
        Route::group(['middleware' => 'instructor'], function () {
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

        //Applications
        Route::get('/application/list', 'AtcTraining\ApplicationsController@viewApplications')->name('application.list');
        Route::get('/application/{application_id}', 'AtcTraining\ApplicationsController@viewApplication')->name('application.view');
        Route::get('/application/{application_id}/withdraw', 'AtcTraining\ApplicationsController@withdrawApplication');

        //Applications View/Accept/Deny
        Route::group(['middleware' => 'staff'], function () {
            Route::get('/training/applications', 'AtcTraining\ApplicationsController@viewAllApplications')->name('training.applications');
            Route::get('/training/applications/{id}', 'AtcTraining\TrainingController@viewApplication')->name('training.viewapplication');
            Route::get('/training/applications/{id}/accept', 'AtcTraining\TrainingController@acceptApplication')->name('training.application.accept');
            Route::get('/training/applications/{id}/deny', 'AtcTraining\TrainingController@denyApplication')->name('training.application.deny');
            Route::post('/training/applications/{id}/', 'AtcTraining\TrainingController@editStaffComment')->name('training.application.savestaffcomment');
        });
    });
    // '/me'
    Route::prefix('dashboard/me')->group(function () {
        Route::get('/editbiography', 'Users\UserController@editBioIndex')->name('me.editbioindex');
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
    Route::group(['prefix' => 'admin/users', 'middleware' => 'staff'], function () {
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
    Route::get('/atcresources', 'Publications\AtcResourcesController@index')->middleware('atc')->name('atcresources.index');

    //Upload and Delete ATC Resources
    Route::group(['middleware' => 'staff'], function () {
        Route::post('/atcresources', 'Publications\AtcResourcesController@uploadResource')->name('atcresources.upload');
        Route::get('/atcresources/delete/{id}', 'Publications\AtcResourcesController@deleteResource')->name('atcresources.delete');
    });

    //View CBT
    Route::prefix('dashboard/training/cbt')->group(function () {
        Route::get('/', 'AtcTraining\CBTController@index')->name('cbt.index');
        Route::get('/module', 'AtcTraining\CBTController@moduleindex')->name('cbt.module');
        Route::get('/module/view/{id}/{progress}', 'AtcTraining\CBTController@viewmodule')->name('cbt.module.view');
        Route::get('/exam', 'AtcTraining\CBTController@examindex')->name('cbt.exam');
        Route::get('/exam/start/{id}', 'AtcTraining\CBTController@startExam')->name('cbt.exam.begin');
        Route::get('/exam/{id}', 'AtcTraining\CBTController@exam')->name('cbt.exam.start');
        Route::post('exam/save/{id}', 'AtcTraining\CBTController@saveAnswer')->name('cbt.exam.answer');
        //Instructor
        Route::group(['middleware' => 'instructor'], function () {
            Route::post('/exam/assign', 'AtcTraining\CBTController@examassign')->name('cbt.exam.assign');
            Route::post('/module/assign', 'AtcTraining\CBTController@moduleassign')->name('cbt.module.assign');

        });
        //Staff/Admin
        Route::group(['middleware' => 'staff'], function () {
          Route::get('/module/admin/{id}', 'AtcTraining\CBTController@viewAdminModule')->name('cbt.module.view.admin');
            //  Route::get('/editmodules', 'AtcTraining\CBTController@adminModuleIndex')->name('cbt.admin.module');
            //  Route::get('/editexams', 'AtcTraining\CBTController@adminExamIndex')->name('cbt.admin.exam');
        });
    });

    //ADMIN ONLY
    //Minutes
    Route::group(['middleware' => 'executive'], function () {
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
            Route::get('/site-information', 'Settings\SettingsController@siteInformation')->name('settings.siteinformation');
            Route::post('/site-information', 'Settings\SettingsController@saveSiteInformation')->name('settings.siteinformation.post');
            Route::get('/emails', 'Settings\SettingsController@emails')->name('settings.emails');
            Route::post('/emails', 'Settings\SettingsController@saveEmails')->name('settings.emails.post');
            Route::get('/audit-log', 'Settings\SettingsController@auditLog')->name('settings.auditlog');
            Route::get('/rotation-images', 'Settings\SettingsController@rotationImages')->name('settings.rotationimages');
            Route::get('/rotation-images/delete/{image_id}', 'Settings\SettingsController@deleteRotationImage')->name('settings.rotationimages.deleteimg');
            Route::post('/rotation-images/uploadimg', 'Settings\SettingsController@uploadRotationImage')->name('settings.rotationimages.uploadimg');
            Route::get('/staff', 'Users\StaffListController@editIndex')->name('settings.staff');
            Route::post('/staff/{id}', 'Users\StaffListController@editStaffMember')->name('settings.staff.editmember');
            Route::post('/staff/a/add', 'Users\StaffListController@addStaffMember')->name('settings.staff.addmember');
            Route::post('/staff/{id}/delete', 'Users\StaffListController@deleteStaffMember')->name('settings.staff.deletemember');
            Route::get('/banner', 'Settings\SettingsController@banner')->name('settings.banner');
            Route::post('/banner', 'Settings\SettingsController@bannerEdit')->name('settings.banner.edit');

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
Route::group(['middleware' => 'instructor'], function () {
    Route::get('/dashboard/training/sessions', 'AtcTraining\TrainingController@instructingSessionsIndex')->name('training.instructingsessions.index');
    Route::get('/dashboard/training/sessions/{id}', 'AtcTraining\TrainingController@viewInstructingSession')->name('training.instructingsessions.viewsession');
    Route::view('/dashboard/training/sessions/create', 'dashboard.training.instructingsessions.create')->name('training.instructingsessions.createsessionindex');
    Route::get('/dashboard/training/sessions/create', 'AtcTraining\TrainingController@createInstructingSession')->name('training.instructingsessions.createsession');
    Route::get('/dashboard/training/instructors', 'AtcTraining\TrainingController@instructorsIndex')->name('training.instructors');
    Route::get('/dashboard/training/students/current', 'AtcTraining\TrainingController@currentStudents')->name('training.students.current');
    Route::get('/dashboard/training/students/new', 'AtcTraining\TrainingController@newStudents')->name('training.students.new');
    Route::get('/dashboard/training/students/completed', 'AtcTraining\TrainingController@completedStudents')->name('training.students.completed');
    Route::get('/dashboard/training/students/{id}', 'AtcTraining\TrainingController@viewStudent')->name('training.students.view');
    Route::post('/dashboard/training/students/{id}/assigninstructor', 'AtcTraining\TrainingController@assignInstructorToStudent')->name('training.students.assigninstructor');
    Route::post('/dashboard/training/students/{id}/setstatus', 'AtcTraining\TrainingController@changeStudentStatus')->name('training.students.setstatus');
    Route::get('/dashboard/trainingnotes/{id}', 'AtcTraining\TrainingController@viewNote')->name('trainingnote.view');
    //  Route::get('/dashboard/trainingnotes/{id}/delete', 'AtcTraining\TrainingNotesController@delete')->name('trainingnotes.delete');
    Route::post('/dashboard/trainingnotes/add/{id}', 'AtcTraining\TrainingController@addNote')->name('add.trainingnote');
    Route::get('/dashboard/trainingnotes/create/{id}', 'AtcTraining\TrainingController@newNoteView')->name('view.add.note');

    //AtcTraining
    Route::post('/dashboard/training/instructors', 'AtcTraining\TrainingController@addInstructor')->name('training.instructors.add');
});
