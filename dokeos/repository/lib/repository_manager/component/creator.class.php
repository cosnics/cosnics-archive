<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/../../abstractlearningobject.class.php';

class RepositoryManagerCreatorComponent extends RepositoryManagerComponent
{
	function run()
	{
		$type_form = new FormValidator('create_type', 'post', $this->get_url());
		$type_options = array ();
		$type_options[''] = '';
		foreach ($this->get_learning_object_types() as $type)
		{
			$type_options[$type] = get_lang($type.'TypeName');
		}
		asort($type_options);
		$type_form->addElement('select', RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE, get_lang('CreateANew'), $type_options);
		$type_form->addElement('submit', 'submit', get_lang('Go'));
		$type = ($type_form->validate() ? $type_form->exportValue(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE) : $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE]);
		if ($type)
		{
			$object = new AbstractLearningObject($type, $this->get_user_id(), $_REQUEST[RepositoryManager :: PARAM_CATEGORY_ID]);
			$lo_form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $object, 'create', 'post', $this->get_url(array(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE => $type)));
			if ($lo_form->validate())
			{
				$object = $lo_form->create_learning_object();
				$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, get_lang('ObjectCreated'), $object->get_parent_id());
			}
			else
			{
				$this->display_header();
				$lo_form->display();
				$this->display_footer();
			}
		}
		else
		{
			$renderer = clone $type_form->defaultRenderer();
			$renderer->setElementTemplate('{label} {element} ');
			$type_form->accept($renderer);
			$this->display_header();
			echo $renderer->toHTML();
			$this->display_footer();
		}
	}
}
?>