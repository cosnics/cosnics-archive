<?php
/**
 * @package assessment.datamanager
 */
require_once dirname(__FILE__).'/../assessment_publication.class.php';
require_once dirname(__FILE__).'/../survey_invitation.class.php';
require_once dirname(__FILE__).'/../category_manager/assessment_publication_category.class.php';
require_once dirname(__FILE__).'/../assessment_publication_group.class.php';
require_once dirname(__FILE__).'/../assessment_publication_user.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 *  @author 
 */

class DatabaseAssessmentDataManager extends AssessmentDataManager
{
	private $database;

	function initialize()
	{
		$aliases = array();
		$aliases[AssessmentPublication :: get_table_name()] = 'ason';
		$aliases[AssessmentPublicationGroup :: get_table_name()] = 'asup';
		$aliases[AssessmentPublicationUser :: get_table_name()] = 'aser';

		$this->database = new Database($aliases);
		$this->database->set_prefix('assessment_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	function get_next_assessment_publication_id()
	{
		return $this->database->get_next_id(AssessmentPublication :: get_table_name());
	}

	function create_assessment_publication($assessment_publication)
	{
		return $this->database->create($assessment_publication);
	}

	function update_assessment_publication($assessment_publication)
	{
		$condition = new EqualityCondition(AssessmentPublication :: PROPERTY_ID, $assessment_publication->get_id());
		return $this->database->update($assessment_publication, $condition);
	}

	function delete_assessment_publication($assessment_publication)
	{
		$condition = new EqualityCondition(AssessmentPublication :: PROPERTY_ID, $assessment_publication->get_id());
		return $this->database->delete($assessment_publication->get_table_name(), $condition);
	}

	function count_assessment_publications($condition = null)
	{
		return $this->database->count_objects(AssessmentPublication :: get_table_name(), $condition);
	}

	function get_next_assessment_publication_category_id()
	{
		return $this->database->get_next_id(AssessmentPublicationCategory :: get_table_name());
	}
	
	function create_assessment_publication_category($assessment_category)
	{
		return $this->database->create($assessment_category);
	}
	
	function update_assessment_publication_category($assessment_category)
	{
		$condition = new EqualityCondition(AssessmentPublicationCategory :: PROPERTY_ID, $assessment_category->get_id());
		return $this->database->update($assessment_category, $condition);
	}
	
	function delete_assessment_publication_category($assessment_category)
	{
		$condition = new EqualityCondition(AssessmentPublicationCategory :: PROPERTY_ID, $assessment_category->get_id());
		return $this->database->delete($assessment_category->get_table_name(), $condition);
	}
	
	function count_assessment_publication_categories($condition = null)
	{
		return $this->database->count_objects(AssessmentPublicationCategory :: get_table_name(), $condition);
	}
	
	function retrieve_assessment_publication_category($id)
	{
		$condition = new EqualityCondition(AssessmentPublicationCategory :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(AssessmentPublicationCategory :: get_table_name(), $condition);
	}
	
	function retrieve_assessment_publication_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->database->retrieve_objects(AssessmentPublicationCategory :: get_table_name(), $condition, $offset, $count, $order_property, $order_direction);
	}
	
	function select_next_assessment_publication_category_display_order($parent)
	{
		$query = 'SELECT MAX(' . AssessmentPublicationCategory :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' . $this->database->escape_table_name('assessment_publication_category');

        $condition = new EqualityCondition(AssessmentPublicationCategory :: PROPERTY_PARENT, $parent);

        $params = array();
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this->database, $params);
            $query .= $translator->render_query($condition);
            $params = $translator->get_parameters();
        }

        $sth = $this->database->get_connection()->prepare($query);
        $res = $sth->execute($params);
        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
        $res->free();

        return $record[0] + 1;
	}
	
	function retrieve_assessment_publication($id)
	{
		$condition = new EqualityCondition(AssessmentPublication :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(AssessmentPublication :: get_table_name(), $condition, array(), array(), AssessmentPublication :: CLASS_NAME);
	}

	function retrieve_assessment_publications($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
	{
        $rdm = RepositoryDataManager :: get_instance();
        $publication_alias = $this->database->get_alias(AssessmentPublication :: get_table_name());
        $object_alias = $this->database->get_alias(LearningObject :: get_table_name());

        $query  = 'SELECT ' . $publication_alias . '.* FROM ' . $this->database->escape_table_name(AssessmentPublication :: get_table_name()) . ' AS ' . $publication_alias;
        $query .= ' JOIN ' . $rdm->get_database()->escape_table_name(LearningObject :: get_table_name()) . ' AS ' . $object_alias;
        $query .= ' ON ' . $this->database->escape_column_name(AssessmentPublication :: PROPERTY_LEARNING_OBJECT, $publication_alias) . ' = ';
        $query .= $rdm->get_database()->escape_column_name(LearningObject :: PROPERTY_ID, $object_alias);
		
		return $this->database->retrieve_result_set($query, AssessmentPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, AssessmentPublication :: CLASS_NAME);
	}

	function get_next_assessment_publication_group_id()
	{
		return $this->database->get_next_id(AssessmentPublicationGroup :: get_table_name());
	}

	function create_assessment_publication_group($assessment_publication_group)
	{
		return $this->database->create($assessment_publication_group);
	}

	function update_assessment_publication_group($assessment_publication_group)
	{
		$condition = new EqualityCondition(AssessmentPublicationGroup :: PROPERTY_ID, $assessment_publication_group->get_id());
		return $this->database->update($assessment_publication_group, $condition);
	}

	function delete_assessment_publication_group($assessment_publication_group)
	{
		$condition = new EqualityCondition(AssessmentPublicationGroup :: PROPERTY_ID, $assessment_publication_group->get_id());
		return $this->database->delete($assessment_publication_group->get_table_name(), $condition);
	}

	function count_assessment_publication_groups($condition = null)
	{
		return $this->database->count_objects(AssessmentPublicationGroup :: get_table_name(), $condition);
	}

	function retrieve_assessment_publication_group($id)
	{
		$condition = new EqualityCondition(AssessmentPublicationGroup :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(AssessmentPublicationGroup :: get_table_name(), $condition);
	}

	function retrieve_assessment_publication_groups($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
	{
		return $this->database->retrieve_objects(AssessmentPublicationGroup :: get_table_name(), $condition, $offset, $max_objects, $order_by, $order_dir);
	}

	function get_next_assessment_publication_user_id()
	{
		return $this->database->get_next_id(AssessmentPublicationUser :: get_table_name());
	}

	function create_assessment_publication_user($assessment_publication_user)
	{
		return $this->database->create($assessment_publication_user);
	}

	function update_assessment_publication_user($assessment_publication_user)
	{
		$condition = new EqualityCondition(AssessmentPublicationUser :: PROPERTY_ID, $assessment_publication_user->get_id());
		return $this->database->update($assessment_publication_user, $condition);
	}

	function delete_assessment_publication_user($assessment_publication_user)
	{
		$condition = new EqualityCondition(AssessmentPublicationUser :: PROPERTY_ID, $assessment_publication_user->get_id());
		return $this->database->delete($assessment_publication_user->get_table_name(), $condition);
	}

	function count_assessment_publication_users($condition = null)
	{
		return $this->database->count_objects(AssessmentPublicationUser :: get_table_name(), $condition);
	}

	function retrieve_assessment_publication_user($id)
	{
		$condition = new EqualityCondition(AssessmentPublicationUser :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(AssessmentPublicationUser :: get_table_name(), $condition);
	}

	function retrieve_assessment_publication_users($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
	{
		return $this->database->retrieve_objects(AssessmentPublicationUser :: get_table_name(), $condition, $offset, $max_objects, $order_by, $order_dir);
	}
	
	function get_next_survey_invitation_id()
	{
		return $this->database->get_next_id(SurveyInvitation :: get_table_name());
	}
	
	function create_survey_invitation($survey_invitation)
	{
		return $this->database->create($survey_invitation);
	}
	
	function update_survey_invitation($survey_invitation)
	{
		$condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $survey_invitation->get_id());
		return $this->database->update($survey_invitation, $condition);
	}
	
	function delete_survey_invitation($survey_invitation)
	{
		$condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $survey_invitation->get_id());
		return $this->database->delete($survey_invitation->get_table_name(), $condition);
	}
	
	function count_survey_invitations($condition = null)
	{
		return $this->database->count_objects(SurveyInvitation :: get_table_name(), $condition);
	}
	
	function retrieve_survey_invitation($id)
	{
		$condition = new EqualityCondition(SurveyInvitation :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(SurveyInvitation :: get_table_name(), $condition);
	}
	
	function retrieve_survey_invitations($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->database->retrieve_objects(SurveyInvitation :: get_table_name(), $condition, $offset, $count, $order_property, $order_direction);
	}
}
?>