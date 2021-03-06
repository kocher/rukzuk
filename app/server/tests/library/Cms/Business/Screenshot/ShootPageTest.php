<?php
namespace Cms\Business\Screenshot;

use Cms\Business\Screenshot as ScreenshotBusiness;
use Test\Seitenbau\ServiceTestCase;
use Seitenbau\Registry;

/**
 * Tests fuer shootPage Funktionalitaet Cms\Business\Screen
 *
 * @package      Cms
 * @subpackage   Business\Screenshot
 */

class ShootPageTest extends ServiceTestCase
{
  protected $business;

  protected $websiteId;

  protected function setUp()
  {
    parent::setUp();

    $this->markTestSkipped('Disabled because of memory problems');

    // activate
    Registry::getConfig()->screens->activ = 'yes';
    
    $this->business = new ScreenshotBusiness('Screenshot');

    $this->websiteId = 'SITE-renderer-site-test-1234-mjsncgmjszt1-SITE';
  }

  public function tearDown()
  {
    $websiteDir = Registry::getConfig()->screens->directory .
                  DIRECTORY_SEPARATOR . $this->websiteId . DIRECTORY_SEPARATOR;
    $this->removeDir($websiteDir);

    parent::tearDown();
  }

  /**
   * @test
   * @group library
   */
  public function success()
  {
    if (!$this->testIsActiv())
    {
      $this->markTestSkipped('Screenshot must be activ in config for this test');
    }

    // Page
    $pageId = 'PAGE-renderer-page-1472-47du-m7j9klswmc71-PAGE';

    // Pfade
    $websiteDir = Registry::getConfig()->screens->directory .
                  DIRECTORY_SEPARATOR . $this->websiteId;
    $pagesDir = $websiteDir . DIRECTORY_SEPARATOR . 'pages';
    $templatesDir = $websiteDir . DIRECTORY_SEPARATOR . 'templates';

    // Website-Dir loeschen
    $this->removeDir($websiteDir);

    // Website-Dir darf noch nicht angelegt sein
    $this->assertFileNotExists($websiteDir);

    // Screenshot erstellen
    $this->business->shootPage($this->websiteId, $pageId);

    // Pruefung der Ergebnisse
    $this->assertFileExists($websiteDir);
    $this->assertFileExists($pagesDir);
    $this->assertFileExists($templatesDir);

    sleep(4);

    $this->assertFileExists($pagesDir . DIRECTORY_SEPARATOR . $pageId .
      '.' . Registry::getConfig()->screens->filetype);
  }
    
  /**
   * Prueft anhand der Config, ob der Test ueberhaupt durchgefuehrt werden kann
   *
   * @return boolean
   */
  public function testIsActiv()
  {
    if (Registry::getConfig()->screens->activ == 'yes'
        || Registry::getConfig()->screens->activ == '1')
    {
      return true;
    }
    return false;
  }

  /**
   * Loescht ein Verzeichnis samt Inhalt (Dateien und Unterordner)
   *
   * @param string $websiteDir
   */
  private function removeDir($dir)
  {
    if (\is_dir($dir))
    {
      $dirHandle = opendir($dir);
      while(($file = \readdir($dirHandle)) !== false)
      {
        if ($file == '.' || $file == '..') continue;
        $handle = $dir . DIRECTORY_SEPARATOR . $file;
        
        $filetype = filetype($handle);

        if ($filetype == 'dir')
        {
          $this->removeDir($handle);
        }
        else
        {
          unlink($handle);
        }
      }
      closedir($dirHandle);
      rmdir($dir);
    }
  }
}