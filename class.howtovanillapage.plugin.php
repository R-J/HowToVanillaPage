<?php

$PluginInfo['howToVanillaPage'] = [
    'Name' => 'HowTo: Vanilla Page',
    'Description' => 'Example for creating a custom page that looks like most other pages in Vanilla.',
    'Version' => '0.2',
    'RequiredApplications' => ['Vanilla' => '>= 2.3'],
    'RequiredTheme' => false,
    'MobileFriendly' => true,
    'HasLocale' => false,
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'http://vanillaforums.org/profile/r_J',
    'License' => 'MIT'
];

/**
 * Example for a custom page in the look and feel of any other Vanilla page.
 *
 * In order to create a custom page that is not too ugly, you would expect it to
 * have the same template like your forum and also some modules in the panel.
 * Furthermore it shouldn't have a clumsy url. So here is an example of how to
 * achieve that.
 *
 * @package howToVanillaPage
 * @author Robin Jurinka
 * @license MIT
 */
class HowToVanillaPagePlugin extends Gdn_Plugin {
    // This is just to make handling easy.
    // If you are toying around with this plugin, you have to keep in mind that
    // before you change one of the values, you have to disable the plugin
    // (to delete the current route), then change the values below and then
    // reenable the plugin to set the new route.
    const SHORT_ROUTE = 'hello';
    const LONG_ROUTE = 'vanilla/howtovanillapage';

    // Normally you wouldn't use constants for all that but use the words you
    // need right in the cade. I've chosen to do it that way, because I think it
    // makes code reading easier.
    const PAGE_NAME = 'Greetings';

    /**
     * Setup is run whenever plugin is enabled.
     *
     * This is the best place to create a custom route for our page. That will
     * make a pretty url for a otherwise clumsy slug.
     *
     * @return void.
     * @package HowToVanillaPage
     * @since 0.1
     */
    public function setup() {
        // get a reference to Vanillas routing class
        $router = Gdn::router();

        // this is the ugly slug we want to change
        $pluginPage = self::LONG_ROUTE.'$1';

        // that's how the nice url should look like
        $newRoute = '^'.self::SHORT_ROUTE.'(/.*)?$';

        // "route 'yourforum.com/vanillacontroller/howtovanillapage' to
        // 'yourforum.com/fancyShortName'"
        if (!$router->matchRoute($newRoute)) {
            $router->setRoute($newRoute, $pluginPage, 'Internal');
        }
    }


    /**
     * OnDisable is run whenever plugin is disabled.
     *
     * We have to delete our internal route because our custom page will not be
     * accessible any more.
     *
     * @return void.
     */
    public function onDisable() {
        // easy as that:
        Gdn::router()->deleteRoute('^'.self::SHORT_ROUTE.'(/.*)?$');
    }


    /**
     * Create a link to our page in the menu.
     *
     * @param GardenController $sender Instance of the calling class.
     *
     * @return void.
     */
    public function base_render_before($sender) {
        // We only need to add the menu entry if a) the controller has a menu
        // and b) we are not in the admin area.
        if ($sender->Menu && $sender->masterView() != 'admin') {
            // If current page is our custom page, we want the menu entry
            // to be selected. This is only needed if you've changed the route,
            // otherwise it will happen automatically.
            if ($sender->SelfUrl == self::SHORT_ROUTE) {
                $anchorAttributes = ['class' => 'Selected'];
            } else {
                $anchorAttributes = '';
            }

            // We add our Link to a section (but you can pass an empty string
            // if there is no group you like to add your link to), pass a name,
            // the link target and our class to the function.
            $sender->Menu->addLink(
                '',
                t(self::PAGE_NAME),
                self::SHORT_ROUTE,
                '',
                $anchorAttributes
            );
        }
    }

    /**
     * Create a new page that uses the current theme.
     *
     * By extending the Vanilla controller, we have access to all resources
     * that we need.
     *
     * @param VanillaController $sender Instance of the calling class.
     * @param array             $args   Arguments for our function passed in the url.
     *
     * @return void.
     */
    public function vanillaController_howToVanillaPage_create($sender, $args) {
        // That one is critical! The template of your theme is called
        // default.master.tpl and calling this function sets the master view of
        // this controller to the default theme template.
        $sender->masterView();

        // If you've changed the route, you should change that value, too. We
        // use it for highlighting the menu entry.
        $sender->SelfUrl = self::SHORT_ROUTE;

        // If you need custom CSS or Javascript, create the files in the
        // subfolders "design" (for CSS) and "js" (for Javascript). The naming
        // of the files is completely up to you. You can load them by
        // uncommenting the respective line below.
        // $sender->addCssFile('howtovanillapage.css', 'plugins/HowToVanillaPage');
        // $sender->addJsFile('howtovanillapage.js', 'plugins/HowToVanillaPage');

        // There is a list of which modules to add to the panel for a standard
        // Vanilla page. We will add all of them, just to be sure our new page
        // looks familiar to the users.
        foreach (c('Modules.Vanilla.Panel') as $module) {
            // We have to exclude the MeModule here, because it is already added
            // by the template and it would appear twice otherwise.
            if ($module != 'MeModule') {
                $sender->addModule($module);
            }
        }

        // We can set a title for the page like that. But this is just a short
        // form for $sender->setData('Title', 'Vanilla Page');
        $sender->title(t(self::PAGE_NAME));

        // This sets the breadcrumb to our current page.
        $sender->setData(
            'Breadcrumbs',
            [['Name' => t(self::PAGE_NAME), 'Url' => self::SHORT_ROUTE]]
        );

        // If you would like to pass some other data to your view, you should do
        // it with setData. Let's do a "Hello World"...
        if ($args[0] != '') {
            // We will use this for a conditional output.
            $sender->setData('hasArguments', true);
            // If we have a parameter use this.
            $name = $args[0];
        } else {
            // We will use this for a conditional output.
            $sender->setData('hasArguments', false);

            $session = Gdn::session();
            if ($session->isValid()) {
                // If user is logged in, get his name.
                $name = $session->User->Name;
            } else {
                // No parameter and no user name? We determine a name by ourselves
                $name = t('Anonymous');
            }
        }

        // Let's pass this example to our view.
        $sender->setData('name', $name);

        // We could have simply echoed to screen here, but Garden is a MVC
        // framework and that's why we should use a separate view if possible.
        $sender->render($sender->fetchViewLocation('howtovanillapage', '', 'plugins/howToVanillaPage'));
    }
}
