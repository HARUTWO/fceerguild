<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use App\Policies\ReferenceTablePolicy;
use App\Policies\ProfilePolicy;
use App\Policies\RosterPolicy;
use App\Policies\AttendancePolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load profile configs from subfolder
        config(['profile.personal' => require config_path('profile/personal.php')]);
        config(['profile.account' => require config_path('profile/account.php')]);
        config(['profile.credentials' => require config_path('profile/credentials.php')]);
        config(['profile.fceer' => require config_path('profile/fceer.php')]);

        // Register custom Livewire aliases for dynamic reference CRUD
        if (class_exists(Livewire::class)) {
            Livewire::component('reference-crud', \App\Http\Livewire\ReferenceCrud::class);

            // Roster components
            Livewire::component('roster.roster-table', \App\Http\Livewire\Roster\RosterTable::class);
            Livewire::component('roster.roster-toolbar', \App\Http\Livewire\Roster\RosterToolbar::class);
            Livewire::component('roster.roster-archive-modal', \App\Http\Livewire\Roster\RosterArchiveModal::class);
            Livewire::component('roster.roster-archive-restore-modal', \App\Http\Livewire\Roster\RosterArchiveRestoreModal::class);
            Livewire::component('roster.roster-user-form-modal', \App\Http\Livewire\Roster\RosterUserFormModal::class);
            Livewire::component('roster.roster-delete-modal', \App\Http\Livewire\Roster\RosterDeleteModal::class);
            Livewire::component('roster.roster-restore-modal', \App\Http\Livewire\Roster\RosterRestoreModal::class);
            Livewire::component('roster.roster-force-delete-modal', \App\Http\Livewire\Roster\RosterForceDeleteModal::class);

            // Attendance components
            Livewire::component('attendance.export-modal', \App\Livewire\Attendance\ExportModal::class);
            Livewire::component('attendance.review-season-modal', \App\Livewire\Attendance\ReviewSeasonModal::class);

            // Explicitly register profile components
            Livewire::component('profile.show', \App\Http\Livewire\Profile\Show::class);
            Livewire::component('profile.personal-records', \App\Http\Livewire\Profile\Personal\PersonalRecords::class);
            Livewire::component('profile.personal-form-modal', \App\Http\Livewire\Profile\Personal\PersonalFormModal::class);
            
            // Personal subsections
            Livewire::component('profile.personal.subsections.identification.identification', \App\Http\Livewire\Profile\Personal\Subsections\Identification\Identification::class);
            Livewire::component('profile.personal.subsections.identification.identification-form-modal', \App\Http\Livewire\Profile\Personal\Subsections\Identification\IdentificationFormModal::class);
            Livewire::component('profile.personal.subsections.identification.identification-confirm-modal', \App\Http\Livewire\Profile\Personal\Subsections\Identification\IdentificationConfirmModal::class);
            Livewire::component('profile.personal.subsections.contact-details.contact-details', \App\Http\Livewire\Profile\Personal\Subsections\ContactDetails\ContactDetails::class);
            Livewire::component('profile.personal.subsections.contact-details.contact-details-form-modal', \App\Http\Livewire\Profile\Personal\Subsections\ContactDetails\ContactDetailsFormModal::class);
            Livewire::component('profile.personal.subsections.contact-details.contact-details-confirm-modal', \App\Http\Livewire\Profile\Personal\Subsections\ContactDetails\ContactDetailsConfirmModal::class);
            Livewire::component('profile.personal.subsections.address.address', \App\Http\Livewire\Profile\Personal\Subsections\Address\Address::class);
            Livewire::component('profile.personal.subsections.address.address-form-modal', \App\Http\Livewire\Profile\Personal\Subsections\Address\AddressFormModal::class);
            Livewire::component('profile.personal.subsections.address.address-confirm-modal', \App\Http\Livewire\Profile\Personal\Subsections\Address\AddressConfirmModal::class);
            
            Livewire::component('profile.account-records', \App\Http\Livewire\Profile\AccountRecords::class);
            
            // Account subsections
            Livewire::component('profile.account.subsections.account-information.account-information', \App\Http\Livewire\Profile\Account\Subsections\AccountInformation\AccountInformation::class);
            Livewire::component('profile.account.subsections.profile-picture.profile-picture', \App\Http\Livewire\Profile\Account\Subsections\ProfilePicture\ProfilePicture::class);
            Livewire::component('profile.credentials', \App\Http\Livewire\Profile\Credentials::class);
            Livewire::component('profile.fceer-records', \App\Http\Livewire\Profile\FceerRecords::class);
            Livewire::component('profile-crud', \App\Http\Livewire\Profile\ProfileCrud::class);
            Livewire::component('profile-form-modal', \App\Http\Livewire\Profile\ProfileFormModal::class);
            Livewire::component('profile.credentials-form-modal', \App\Http\Livewire\Profile\CredentialsFormModal::class);


            // Newly scaffolded profile subsection components (stubs)
            Livewire::component('profile.fceer.subsections.subject-teachers.subject-teachers', \App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers\SubjectTeachers::class);
            Livewire::component('profile.fceer.subsections.subject-teachers.subject-teachers-form-modal', \App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers\SubjectTeachersFormModal::class);
            Livewire::component('profile.fceer.subsections.subject-teachers.subject-teachers-details-modal', \App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers\SubjectTeachersDetailsModal::class);
            Livewire::component('profile.fceer.subsections.subject-teachers.subject-teachers-archive-modal', \App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers\SubjectTeachersArchiveModal::class);
            Livewire::component('profile.fceer.subsections.subject-teachers.subject-teacher-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers\SubjectTeacherDeleteModal::class);
            Livewire::component('profile.fceer.subsections.subject-teachers.subject-teacher-force-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers\SubjectTeacherForceDeleteModal::class);
            Livewire::component('profile.fceer.subsections.subject-teachers.subject-teacher-restore-modal', \App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers\SubjectTeacherRestoreModal::class);
            Livewire::component('profile.fceer.subsections.subject-teachers.subject-teachers-confirm-modal', \App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers\SubjectTeachersConfirmModal::class);

            Livewire::component('profile.fceer.subsections.committee-memberships.committee-memberships', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMemberships::class);
            Livewire::component('profile.fceer.subsections.committee-memberships.committee-memberships-form-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsFormModal::class);
            Livewire::component('profile.fceer.subsections.committee-memberships.committee-memberships-details-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsDetailsModal::class);
            Livewire::component('profile.fceer.subsections.committee-memberships.committee-memberships-archive', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsArchive::class);
            Livewire::component('profile.fceer.subsections.committee-memberships.committee-memberships-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsDeleteModal::class);
            Livewire::component('profile.fceer.subsections.committee-memberships.committee-memberships-restore-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsRestoreModal::class);
            Livewire::component('profile.fceer.subsections.committee-memberships.committee-memberships-force-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsForceDeleteModal::class);
            Livewire::component('profile.fceer.subsections.committee-memberships.committee-memberships-confirm-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsConfirmModal::class);

            Livewire::component('profile.fceer.subsections.committee_memberships.committee_memberships', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMemberships::class);
            Livewire::component('profile.fceer.subsections.committee_memberships.committee_memberships-form-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsFormModal::class);
            Livewire::component('profile.fceer.subsections.committee_memberships.committee_memberships-details-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsDetailsModal::class);
            Livewire::component('profile.fceer.subsections.committee_memberships.committee_memberships-archive', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsArchive::class);
            Livewire::component('profile.fceer.subsections.committee_memberships.committee_memberships-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsDeleteModal::class);
            Livewire::component('profile.fceer.subsections.committee_memberships.committee_memberships-restore-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsRestoreModal::class);
            Livewire::component('profile.fceer.subsections.committee_memberships.committee_memberships-force-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsForceDeleteModal::class);
            Livewire::component('profile.fceer.subsections.committee_memberships.committee_memberships-confirm-modal', \App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships\CommitteeMembershipsConfirmModal::class);

            Livewire::component('profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilities::class);
            Livewire::component('profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-form-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesFormModal::class);
            Livewire::component('profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-details-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesDetailsModal::class);
            Livewire::component('profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-archive', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesArchive::class);
            Livewire::component('profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesDeleteModal::class);
            Livewire::component('profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-restore-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesRestoreModal::class);
            Livewire::component('profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-force-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesForceDeleteModal::class);
            Livewire::component('profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-confirm-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesConfirmModal::class);
      
            Livewire::component('profile.fceer.subsections.classroom_responsibilities.classroom_responsibilities', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilities::class);
            Livewire::component('profile.fceer.subsections.classroom_responsibilities.classroom_responsibilities-form-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesFormModal::class);
            Livewire::component('profile.fceer.subsections.classroom_responsibilities.classroom_responsibilities-details-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesDetailsModal::class);
            Livewire::component('profile.fceer.subsections.classroom_responsibilities.classroom_responsibilities-archive', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesArchive::class);
            Livewire::component('profile.fceer.subsections.classroom_responsibilities.classroom_responsibilities-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesDeleteModal::class);
            Livewire::component('profile.fceer.subsections.classroom_responsibilities.classroom_responsibilities-restore-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesRestoreModal::class);
            Livewire::component('profile.fceer.subsections.classroom_responsibilities.classroom_responsibilities-force-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesForceDeleteModal::class);
            Livewire::component('profile.fceer.subsections.classroom_responsibilities.classroom_responsibilities-confirm-modal', \App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities\ClassroomResponsibilitiesConfirmModal::class);

                // FCEER profiles subsection aliases
                Livewire::component('profile.fceer.subsections.fceer-profiles.fceer-profiles', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfiles::class);
                Livewire::component('profile.fceer.subsections.fceer-profiles.fceer-profiles-form-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesFormModal::class);
                Livewire::component('profile.fceer.subsections.fceer-profiles.fceer-profiles-details-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesDetailsModal::class);
                Livewire::component('profile.fceer.subsections.fceer-profiles.fceer-profiles-archive', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesArchive::class);
                Livewire::component('profile.fceer.subsections.fceer-profiles.fceer-profiles-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesDeleteModal::class);
                Livewire::component('profile.fceer.subsections.fceer-profiles.fceer-profiles-restore-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesRestoreModal::class);
                Livewire::component('profile.fceer.subsections.fceer-profiles.fceer-profiles-force-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesForceDeleteModal::class);

                Livewire::component('profile.fceer.subsections.fceer_profiles.fceer_profiles', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfiles::class);
                Livewire::component('profile.fceer.subsections.fceer_profiles.fceer_profiles-form-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesFormModal::class);
                Livewire::component('profile.fceer.subsections.fceer_profiles.fceer_profiles-details-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesDetailsModal::class);
                Livewire::component('profile.fceer.subsections.fceer_profiles.fceer_profiles-archive', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesArchive::class);
                Livewire::component('profile.fceer.subsections.fceer_profiles.fceer_profiles-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesDeleteModal::class);
                Livewire::component('profile.fceer.subsections.fceer_profiles.fceer_profiles-restore-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesRestoreModal::class);
                Livewire::component('profile.fceer.subsections.fceer_profiles.fceer_profiles-force-delete-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfiles\FceerProfilesForceDeleteModal::class);

                // FceerProfileSection (singular - single profile per user)
                Livewire::component('profile.fceer.subsections.fceer-profile-section.fceer-profile-section', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfileSection\FceerProfileSection::class);
                Livewire::component('profile.fceer.subsections.fceer-profile-section.fceer-profile-form-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfileSection\FceerProfileFormModal::class);
                Livewire::component('profile.fceer.subsections.fceer-profile-section.fceer-profile-confirm-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfileSection\FceerProfileConfirmModal::class);
                Livewire::component('profile.fceer.subsections.fceer-profile-section.fceer-profile-details-modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfileSection\FceerProfileDetailsModal::class);

                Livewire::component('profile.fceer.subsections.fceer_profile_section.fceer_profile_section', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfileSection\FceerProfileSection::class);
                Livewire::component('profile.fceer.subsections.fceer_profile_section.fceer_profile_form_modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfileSection\FceerProfileFormModal::class);
                Livewire::component('profile.fceer.subsections.fceer_profile_section.fceer_profile_confirm_modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfileSection\FceerProfileConfirmModal::class);
                Livewire::component('profile.fceer.subsections.fceer_profile_section.fceer_profile_details_modal', \App\Http\Livewire\Profile\Fceer\Subsections\FceerProfileSection\FceerProfileDetailsModal::class);

            Livewire::component('profile.credentials.subsections.highschool-records.highschool-records', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolRecords\HighschoolRecords::class);
            Livewire::component('profile.credentials.subsections.highschool-records.highschool-records-form-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolRecords\HighschoolRecordsFormModal::class);
            Livewire::component('profile.credentials.subsections.highschool-records.highschool-records-details-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolRecords\HighschoolRecordsDetailsModal::class);

            Livewire::component('profile.credentials.subsections.highschool-records.details-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolRecords\HighschoolRecordsDetailsModal::class);
            Livewire::component('profile.credentials.subsections.highschool-records.highschool-records-archive', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolRecords\HighschoolRecordsArchiveModal::class);

            Livewire::component('profile.credentials.subsections.highschool-records.delete-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolRecords\HighschoolDeleteModal::class);
            Livewire::component('profile.credentials.subsections.highschool-records.restore-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolRecords\HighschoolRestoreModal::class);
            Livewire::component('profile.credentials.subsections.highschool-records.force-delete-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolRecords\HighschoolForceDeleteModal::class);

            Livewire::component('profile.credentials.subsections.highschool-subject-records.highschool-subject-records', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords\HighschoolSubjectRecords::class);
            Livewire::component('profile.credentials.subsections.highschool-subject-records.highschool-subject-records-form-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords\HighschoolSubjectRecordsFormModal::class);
            Livewire::component('profile.credentials.subsections.highschool-subject-records.highschool-subject-records-details-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords\HighschoolSubjectRecordsDetailsModal::class);

            Livewire::component('profile.credentials.subsections.highschool-subject-records.details-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords\HighschoolSubjectRecordsDetailsModal::class);
            Livewire::component('profile.credentials.subsections.highschool-subject-records.highschool-subject-records-archive', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords\HighschoolSubjectRecordsArchiveModal::class);
            Livewire::component('profile.credentials.subsections.highschool-subject-records.highschool-subject-delete-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords\HighschoolSubjectDeleteModal::class);
            Livewire::component('profile.credentials.subsections.highschool-subject-records.highschool-subject-restore-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords\HighschoolSubjectRestoreModal::class);
            Livewire::component('profile.credentials.subsections.highschool-subject-records.highschool-subject-force-delete-modal', \App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords\HighschoolSubjectForceDeleteModal::class);

            Livewire::component('profile.credentials.subsections.educational-records.educational-records', \App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords\EducationalRecords::class);
            Livewire::component('profile.credentials.subsections.educational-records.educational-records-form-modal', \App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords\EducationalRecordsFormModal::class);
            
            Livewire::component('profile.credentials.subsections.educational-records.educational-records-archive', \App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords\EducationalRecordsArchiveModal::class);
            Livewire::component('profile.credentials.subsections.educational-records.educational-records-details-modal', \App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords\EducationalRecordsDetailsModal::class);
            Livewire::component('profile.credentials.subsections.educational-records.delete-modal', \App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords\EducationalDeleteModal::class);
            Livewire::component('profile.credentials.subsections.educational-records.restore-modal', \App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords\EducationalRestoreModal::class);
            Livewire::component('profile.credentials.subsections.educational-records.force-delete-modal', \App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords\EducationalForceDeleteModal::class);

            Livewire::component('profile.credentials.subsections.professional-credentials.professional-credentials', \App\Http\Livewire\Profile\Credentials\Subsections\ProfessionalCredentials\ProfessionalCredentials::class);
            Livewire::component('profile.credentials.subsections.professional-credentials.professional-credentials-form-modal', \App\Http\Livewire\Profile\Credentials\Subsections\ProfessionalCredentials\ProfessionalCredentialsFormModal::class);
            Livewire::component('profile.credentials.subsections.professional-credentials.professional-credentials-details-modal', \App\Http\Livewire\Profile\Credentials\Subsections\ProfessionalCredentials\ProfessionalCredentialsDetailsModal::class);
            Livewire::component('profile.credentials.subsections.professional-credentials.professional-credentials-archive', \App\Http\Livewire\Profile\Credentials\Subsections\ProfessionalCredentials\ProfessionalCredentialsArchiveModal::class);
            Livewire::component('profile.credentials.subsections.professional-credentials.professional-credentials-delete-modal', \App\Http\Livewire\Profile\Credentials\Subsections\ProfessionalCredentials\ProfessionalCredentialsDeleteModal::class);
            Livewire::component('profile.credentials.subsections.professional-credentials.professional-credentials-restore-modal', \App\Http\Livewire\Profile\Credentials\Subsections\ProfessionalCredentials\ProfessionalCredentialsRestoreModal::class);
            Livewire::component('profile.credentials.subsections.professional-credentials.professional-credentials-force-delete-modal', \App\Http\Livewire\Profile\Credentials\Subsections\ProfessionalCredentials\ProfessionalCredentialsForceDeleteModal::class);

            Livewire::component('app.http.livewire.reference.reference-table', \App\Http\Livewire\Reference\ReferenceTable::class);
            Livewire::component('reference.reference-table', \App\Http\Livewire\Reference\ReferenceTable::class);
            Livewire::component('reference-table', \App\Http\Livewire\Reference\ReferenceTable::class);

            Livewire::component('app.http.livewire.reference.reference-form-modal', \App\Http\Livewire\Reference\Modal\ReferenceFormModal::class);
            Livewire::component('reference-form-modal', \App\Http\Livewire\Reference\Modal\ReferenceFormModal::class);

            Livewire::component('app.http.livewire.reference.reference-confirm-changes-modal', \App\Http\Livewire\Reference\Modal\ReferenceConfirmChangesModal::class);
            Livewire::component('reference-confirm-changes-modal', \App\Http\Livewire\Reference\Modal\ReferenceConfirmChangesModal::class);

            Livewire::component('app.http.livewire.reference.reference-details-modal', \App\Http\Livewire\Reference\Modal\ReferenceDetailsModal::class);
            Livewire::component('reference-details-modal', \App\Http\Livewire\Reference\Modal\ReferenceDetailsModal::class);

            Livewire::component('app.http.livewire.reference.reference-delete-modal', \App\Http\Livewire\Reference\Modal\ReferenceDeleteModal::class);
            Livewire::component('reference-delete-modal', \App\Http\Livewire\Reference\Modal\ReferenceDeleteModal::class);
            Livewire::component('app.http.livewire.reference.reference-archive-modal', \App\Http\Livewire\Reference\Modal\ReferenceArchiveModal::class);
            Livewire::component('reference.reference-archive-modal', \App\Http\Livewire\Reference\Modal\ReferenceArchiveModal::class);
            Livewire::component('reference-archive-modal', \App\Http\Livewire\Reference\Modal\ReferenceArchiveModal::class);
            Livewire::component('app.http.livewire.reference.reference-restore-modal', \App\Http\Livewire\Reference\Modal\ReferenceRestoreModal::class);
            Livewire::component('reference-restore-modal', \App\Http\Livewire\Reference\Modal\ReferenceRestoreModal::class);
            Livewire::component('app.http.livewire.reference.reference-force-delete-modal', \App\Http\Livewire\Reference\Modal\ReferenceForceDeleteModal::class);
            Livewire::component('reference-force-delete-modal', \App\Http\Livewire\Reference\Modal\ReferenceForceDeleteModal::class);
            Livewire::component('reference.reference-toolbar', \App\Http\Livewire\Reference\ReferenceToolbar::class);

            // Profile confirmation modal for saving credential/subsection changes
            Livewire::component('profile.profile-confirm-changes-modal', \App\Http\Livewire\Profile\Modal\ProfileConfirmChangesModal::class);
        }

        if (class_exists(Gate::class)) {
            Gate::define('manageReferenceTables', [ReferenceTablePolicy::class, 'manage']);
            Gate::define('viewRoster', [RosterPolicy::class, 'view']);
            Gate::define('createRosterUser', [RosterPolicy::class, 'create']);
            Gate::define('deleteRosterUser', [RosterPolicy::class, 'delete']);
            Gate::define('forceDeleteRosterUser', [RosterPolicy::class, 'forceDelete']);
            Gate::define('restoreRosterUser', [RosterPolicy::class, 'restore']);

            // Attendance gates
            Gate::define('viewAnyAttendance', [AttendancePolicy::class, 'viewAny']);
            Gate::define('createAttendance', [AttendancePolicy::class, 'create']);
            Gate::define('bulkUpdateAttendance', [AttendancePolicy::class, 'bulkUpdate']);
            Gate::define('exportAttendance', [AttendancePolicy::class, 'export']);
            Gate::define('manageReviewSeason', [AttendancePolicy::class, 'manageReviewSeason']);
            
            // View attendance gate - managers (1,2,3) can view any, users can view themselves
            Gate::define('view-attendance', function ($actor, $targetUser) {
                // Managers can view any user
                if (in_array($actor->role_id, [1, 2, 3])) {
                    return true;
                }
                // Users can view their own attendance
                return $actor->id === $targetUser->id;
            });
        }

        if (class_exists(\Livewire\Component::class) && method_exists(\Livewire\Component::class, 'dispatch')) {
            \Livewire\Component::macro('emit', function ($event, ...$params) {
                return $this->dispatch($event, ...$params);
            });
        }

        Gate::policy(\App\Models\User::class, ProfilePolicy::class);
        Gate::policy(\App\Models\AttendanceRecord::class, AttendancePolicy::class);
    }
}
