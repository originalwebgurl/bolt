<?php

use Codeception\Util\Fixtures;
use Codeception\Util\Locator;

/**
 * Backend 'admin' tests
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class BackendAdminCest
{
    /** @var array */
    protected $user;

    /**
     * @param \AcceptanceTester $I
     */
    public function _before(\AcceptanceTester $I)
    {
        $this->user = Fixtures::get('users');
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function _after(\AcceptanceTester $I)
    {
    }

    /**
     * Login the admin user
     *
     * @param \AcceptanceTester $I
     */
    public function loginAdminUserTest(\AcceptanceTester $I)
    {
        $I->wantTo('log into the backend as Admin');

        $I->loginAs($this->user['admin']);
        $I->see('Dashboard');
        $I->see('Configuration', Locator::href('/bolt/users'));
        $I->see("You've been logged on successfully.");
    }

    /**
     * Create a 'editor' user with the 'editor' role
     *
     * @param \AcceptanceTester $I
     */
    public function createEditorTest(\AcceptanceTester $I)
    {
        $I->wantTo("Create a 'editor' user");

        $I->loginAs($this->user['admin']);
        $I->click('Users');
        $I->click('Add a new user', Locator::href('/bolt/users/edit/'));
        $I->see('Create a new user account');

        // Fill in form
        $I->fillField('form[username]',              $this->user['editor']['username']);
        $I->fillField('form[password]',              $this->user['editor']['password']);
        $I->fillField('form[password_confirmation]', $this->user['editor']['password']);
        $I->fillField('form[email]',                 $this->user['editor']['email']);
        $I->fillField('form[displayname]',           $this->user['editor']['displayname']);

        // Add the "editor" role
        $I->checkOption('#form_roles_0');

        // Submit
        $I->click('input[type=submit]');

        // Save is successful?
        $I->see("User {$this->user['editor']['displayname']} has been saved");
    }

    /**
     * Create a 'manager' user with the 'chief-editor' role
     *
     * @param \AcceptanceTester $I
     */
    public function createManagerTest(\AcceptanceTester $I)
    {
        $I->wantTo("Create a 'manager' user");

        $I->loginAs($this->user['admin']);
        $I->click('Users');
        $I->click('Add a new user', Locator::href('/bolt/users/edit/'));
        $I->see('Create a new user account');

        // Fill in form
        $I->fillField('form[username]',              $this->user['manager']['username']);
        $I->fillField('form[password]',              $this->user['manager']['password']);
        $I->fillField('form[password_confirmation]', $this->user['manager']['password']);
        $I->fillField('form[email]',                 $this->user['manager']['email']);
        $I->fillField('form[displayname]',           $this->user['manager']['displayname']);

        // Add the "chief-editor" role
        $I->checkOption('#form_roles_1');

        // Submit
        $I->click('input[type=submit]');

        // Save is successful?
        $I->see("User {$this->user['manager']['displayname']} has been saved");
    }

    /**
     * Create a 'developer' user with the 'developer' role
     *
     * @param \AcceptanceTester $I
     */
    public function createDeveloperTest(\AcceptanceTester $I)
    {
        $I->wantTo("Create a 'developer' user");

        $I->loginAs($this->user['admin']);
        $I->click('Users');
        $I->click('Add a new user', Locator::href('/bolt/users/edit/'));
        $I->see('Create a new user account');

        // Fill in form
        $I->fillField('form[username]',              $this->user['developer']['username']);
        $I->fillField('form[password]',              $this->user['developer']['password']);
        $I->fillField('form[password_confirmation]', $this->user['developer']['password']);
        $I->fillField('form[email]',                 $this->user['developer']['email']);
        $I->fillField('form[displayname]',           $this->user['developer']['displayname']);

        // Add the "developer" role
        $I->checkOption('#form_roles_3');

        // Submit
        $I->click('input[type=submit]');

        // Save is successful?
        $I->see("User {$this->user['developer']['displayname']} has been saved");
    }

    /**
     * Edit site config and set 'canonical', 'notfound' and 'changelog'.
     *
     * @param \AcceptanceTester $I
     */
    public function editConfigTest(\AcceptanceTester $I)
    {
        $I->wantTo("edit config.yml and set 'canonical', 'notfound' and 'changelog'");
        $I->loginAs($this->user['admin']);
        $I->amOnPage('bolt/file/edit/config/config.yml');

        $yaml = $I->getUpdatedConfig();
        $I->fillField('#form_contents', $yaml);
        $I->click('Save', '#saveeditfile');

        $I->see("File 'config.yml' has been saved.");
        $I->see('notfound: resources/not-found');
        $I->see('canonical: example.org');
        $I->see("changelog:\n    enabled: true");
    }

    /**
     * Edit contenttypes.yml and add a 'Resources' Contenttype
     *
     * @param \AcceptanceTester $I
     */
    public function addNewContentTypeTest(\AcceptanceTester $I)
    {
        $I->wantTo("edit contenttypes.yml and add a 'Resources' Contenttype");
        $I->loginAs($this->user['admin']);

        $I->amOnPage('bolt/file/edit/config/contenttypes.yml');
        $yaml = $I->getUpdatedContenttypes();
        $I->fillField('#form_contents', $yaml);
        $I->click('Save');
        $I->see("File 'contenttypes.yml' has been saved.");
        $I->see('name: Resources');
        $I->see('singular_name: Resource');
        $I->see('viewless: true');
    }

    /**
     * Update the database after creating the Resources Contenttype
     *
     * @param \AcceptanceTester $I
     */
    public function updateDatabaseTest(\AcceptanceTester $I)
    {
        $I->wantTo("update the database and add the new 'Resources' Contenttype");
        $I->loginAs($this->user['admin']);

        $I->amOnPage('bolt/dbcheck');

        $I->see('The database needs to be updated/repaired');
        $I->see('is not present');
        $I->see('Update the database', Locator::find('button', array('type' => 'submit')));

        $I->click('Update the database', Locator::find('button', array('type' => 'submit')));
        $I->see('Modifications made to the database');
        $I->see('Created table');
        $I->see('Your database is now up to date');
    }

    /**
     * Update the database after creating the Resources Contenttype
     *
     * @param \AcceptanceTester $I
     */
    public function addNotFoundRecordTest(\AcceptanceTester $I)
    {
        $I->wantTo("create a 404 'not-found' record");
        $I->loginAs($this->user['admin']);

        $I->amOnPage('bolt/editcontent/resources');
        $I->see('New Resource', 'h1');

        $body = \file_get_contents(CODECEPTION_DATA . '/not-found.body.html');

        $I->fillField('#title', '404');
        $I->fillField('#slug',  'not-found');
        $I->fillField('#body',  $body);

        $I->click('Save Resource', '#savecontinuebutton');

        $I->see('Well, this is kind of embarrassing!');
        $I->see('You have what we call in the business, a 404.');
        $I->see('The new Resource has been saved.');
    }

    /**
     * Check that admin user can view all content types
     *
     * @param \AcceptanceTester $I
     */
    public function viewAllContenttypesTest(\AcceptanceTester $I)
    {
        $I->wantTo('make sure the admin user can view all content types');
        $I->loginAs($this->user['admin']);
        $I->click('Dashboard');

        // Pages
        $I->see('Pages',      Locator::href('/bolt/overview/pages'));
        $I->see('View Pages', Locator::href('/bolt/overview/pages'));
        $I->see('New Page',   Locator::href('/bolt/editcontent/pages'));

        // Entries
        $I->see('Entries',      Locator::href('/bolt/overview/entries'));
        $I->see('View Entries', Locator::href('/bolt/overview/entries'));
        $I->see('New Entry',    Locator::href('/bolt/editcontent/entries'));

        // Showcases
        $I->see('Showcases',      Locator::href('/bolt/overview/showcases'));
        $I->see('View Showcases', Locator::href('/bolt/overview/showcases'));
        $I->see('New Showcase',   Locator::href('/bolt/editcontent/showcases'));

        // Resources
        $I->see('Resources',      Locator::href('/bolt/overview/resources'));
        $I->see('View Resources', Locator::href('/bolt/overview/resources'));
        $I->see('New Resource',   Locator::href('/bolt/editcontent/resources'));
    }

    /**
     * Edit site config and set 'canonical', 'notfound' and 'changelog'.
     *
     * @param \AcceptanceTester $I
     */
    public function editConfigTest(\AcceptanceTester $I)
    {
        $I->wantTo("edit config.yml and set 'canonical' and 'notfound'");
        $I->loginAs($this->user['admin']);
        $I->amOnPage('bolt/file/edit/config/config.yml');

        $yaml = $I->getUpdatedConfig();
        $I->fillField('#form_contents', $yaml);
        $I->click('Save', '#saveeditfile');

        $I->see("File 'config.yml' has been saved.");
        $I->see('notfound: resources/not-found');
        $I->see('canonical: example.org');
        $I->see('changelog: true');
    }

    /**
     * Edit site permissions
     *
     * @param \AcceptanceTester $I
     */
    public function editPermissionsTest(\AcceptanceTester $I)
    {
        $I->wantTo('edit permissions.yml and restrict access to certain Contenttypes');
        $I->loginAs($this->user['admin']);
        $I->amOnPage('bolt/file/edit/config/permissions.yml');

        $yaml = $I->getUpdatedPermissions();
        $I->fillField('#form_contents', $yaml);
        $I->click('Save', '#saveeditfile');

        $I->see("File 'permissions.yml' has been saved.");
        $I->see('change-ownership: [ ]');
    }

    /**
     * Edit the taxonomy
     *
     * @param \AcceptanceTester $I
     */
    public function editTaxonomyTest(\AcceptanceTester $I)
    {
        $I->wantTo('edit taxonomy.yml and reorder category options');
        $I->loginAs($this->user['admin']);
        $I->amOnPage('bolt/file/edit/config/taxonomy.yml');

        $yaml = $I->getUpdatedTaxonomy();
        $I->fillField('#form_contents', $yaml);
        $I->click('Save', '#saveeditfile');

        $I->see("File 'taxonomy.yml' has been saved.");
        $I->see('options: [books, events, fun, life, love, movies, music, news]');
    }

    /**
     * Edit the menu file
     *
     * @param \AcceptanceTester $I
     */
    public function editMenuTest(\AcceptanceTester $I)
    {
        $I->wantTo('edit menu.yml and reorder category options');
        $I->loginAs($this->user['admin']);
        $I->amOnPage('bolt/file/edit/config/menu.yml');

        $yaml = $I->getUpdatedMenu();
        $I->fillField('#form_contents', $yaml);
        $I->click('Save', '#saveeditfile');

        $I->see("File 'menu.yml' has been saved.");
        $I->see('Showcases Listing');
        $I->see('path: showcases/');
    }

    /**
     * Edit the routing file
     *
     * @param \AcceptanceTester $I
     */
    public function editRoutingTest(\AcceptanceTester $I)
    {
        $I->wantTo('edit routing.yml and add a pagebinding route');
        $I->loginAs($this->user['admin']);
        $I->amOnPage('bolt/file/edit/config/routing.yml');

        $yaml = $I->getUpdatedRouting();
        $I->fillField('#form_contents', $yaml);
        $I->click('Save', '#saveeditfile');

        $I->see("File 'routing.yml' has been saved.");
        $I->see('pagebinding:');
        $I->see("/{slug}");
        $I->see("contenttype: pages");
    }

    /**
     * Clear the cache
     *
     * @param \AcceptanceTester $I
     */
    public function clearCacheTest(\AcceptanceTester $I)
    {
        $I->wantTo('flush the cache.');
        $I->loginAs($this->user['admin']);
        $I->amOnPage('bolt/clearcache');

        $I->see('Deleted');
        $I->see('files from cache.');
        $I->see('Clear cache again', 'a');
    }

    /**
     * Logout the admin user
     *
     * @param \AcceptanceTester $I
     */
    public function logoutAdminUserTest(\AcceptanceTester $I)
    {
        $I->wantTo('log out of the backend as Admin');

        $I->amOnPage('bolt');
        $I->loginAs($this->user['admin']);

        $I->see('Dashboard');
        $I->click('Logout');

        $I->see('You have been logged out');

        $I->amOnPage('bolt');
        $I->see('Please log on');
    }
}
