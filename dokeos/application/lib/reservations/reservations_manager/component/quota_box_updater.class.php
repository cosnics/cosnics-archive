<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../quota_box.class.php';
require_once dirname(__FILE__).'/../../forms/quota_box_form.class.php';
require_once dirname(__FILE__).'/../../reservations_data_manager.class.php';

class ReservationsManagerQuotaBoxUpdaterComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$quota_box_id = Request :: get(ReservationsManager :: PARAM_QUOTA_BOX_ID);
		$trail = new BreadcrumbTrail();
		$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTA_BOXES)), Translation :: get('ViewQuotaBoxes')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_QUOTA_BOX_ID => $quota_box_id)), Translation :: get('UpdateQuotaBoxes')));

		$user = $this->get_user();

		if (!isset($user)) 
		{
			Display :: display_not_allowed($trail);
			exit;
		} 

		$quota_boxes = $this->retrieve_quota_boxes(new EqualityCondition(QuotaBox :: PROPERTY_ID, $quota_box_id));
		$quota_box = $quota_boxes->next_result();
		
		$form = new QuotaBoxForm(QuotaBoxForm :: TYPE_EDIT, $this->get_url(array(ReservationsManager :: PARAM_QUOTA_BOX_ID => $quota_box_id)), $quota_box, $user);

		if($form->validate())
		{
			$success = $form->update_quota_box();
			$this->redirect(Translation :: get($success ? 'QuotaBoxUpdated' : 'QuotaBoxNotUpdated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTA_BOXES));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>