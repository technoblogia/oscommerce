<?php
/*
  $Id:newsletters.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Account_Newsletters extends osC_Template {

/* Private variables */

    var $_module = 'newsletters',
        $_group = 'account',
        $_page_title = HEADING_TITLE_NEWSLETTERS,
        $_page_contents = 'account_newsletters.php';

/* Class constructor */

    function osC_Account_Newsletters() {
      global $osC_Services, $breadcrumb, $osC_Database, $osC_Customer, $Qnewsletter;

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_NEWSLETTERS, tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

/////////////////////// HPDL /////// Should be moved to the customers class!
      $Qnewsletter = $osC_Database->query('select customers_newsletter from :table_customers where customers_id = :customers_id');
      $Qnewsletter->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qnewsletter->bindInt(':customers_id', $osC_Customer->getID());
      $Qnewsletter->execute();

      if ($_GET[$this->_module] == 'save') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      global $messageStack, $osC_Database, $osC_Customer, $Qnewsletter;

      if (isset($_POST['newsletter_general']) && is_numeric($_POST['newsletter_general'])) {
        $newsletter_general = $_POST['newsletter_general'];
      } else {
        $newsletter_general = '0';
      }

      if ($newsletter_general != $Qnewsletter->valueInt('customers_newsletter')) {
        $newsletter_general = (($Qnewsletter->value('customers_newsletter') == '1') ? '0' : '1');

        $Qupdate = $osC_Database->query('update :table_customers set customers_newsletter = :customers_newsletter where customers_id = :customers_id');
        $Qupdate->bindTable(':table_customers', TABLE_CUSTOMERS);
        $Qupdate->bindInt(':customers_newsletter', $newsletter_general);
        $Qupdate->bindInt(':customers_id', $osC_Customer->getID());
        $Qupdate->execute();

        if ($Qupdate->affectedRows() === 1) {
          $messageStack->add_session('account', SUCCESS_NEWSLETTER_UPDATED, 'success');
        }
      }

      tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
    }
  }
?>
