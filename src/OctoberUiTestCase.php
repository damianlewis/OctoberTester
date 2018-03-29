<?php

namespace DamianLewis\OctoberTester;

use BackendAuth;
use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Symfony\Component\Finder\Finder;

abstract class OctoberUiTestCase extends OctoberTestCase
{
    use Concerns\ProvidesBrowser;

    /**
     * Register the base URL and create a browser instance.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        Browser::$baseUrl = $this->baseUrl();

        Browser::$storeScreenshotsAt = base_path('tests/Browser/screenshots');

        Browser::$storeConsoleLogAt = base_path('tests/Browser/console');

        Browser::$userCredentialsResolver = function () {
            return $this->getUserCredentials();
        };

        $this->purgeScreenshots();
        $this->purgeLogs();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions())->addArguments(config('webdriver.chromeOptions'));

        return RemoteWebDriver::create(
            config('webdriver.host'),
            DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options)
        );
    }

    /**
     * Determine the application's base URL.
     *
     * @return string
     */
    protected function baseUrl()
    {
        return config('webdriver.baseUrl');
    }

    /**
     * Get a callback that returns the default user credentials to authenticate.
     *
     * @return \Closure
     * @throws \Exception
     */
    protected function getUserCredentials()
    {
        throw new Exception("User credentials resolver has not been set.");
    }

    /**
     * Purge the failure screenshots
     *
     * @return void
     */
    protected function purgeScreenshots()
    {
        $files = Finder::create()->files()
            ->in(Browser::$storeScreenshotsAt)
            ->name('failure-*');

        foreach ($files as $file) {
            @unlink($file->getRealPath());
        }
    }

    /**
     * Purge the failure logs
     *
     * @return void
     */
    protected function purgeLogs()
    {
        $files = Finder::create()->files()
            ->in(Browser::$storeConsoleLogAt)
            ->name('*.log');

        foreach ($files as $file) {
            @unlink($file->getRealPath());
        }
    }
}