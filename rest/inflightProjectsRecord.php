<?php
namespace rest;

use itdq\DbRecord;

/**
 *
 * @author gb001399
 *
 */
class inflightProjectsRecord extends DbRecord
{
    protected $CIO;
    protected $PRN;
    protected $PROJECTNAME;
    protected $PROJECTNUMBER;
    protected $CURRENTPLATFORM;
    protected $RESOURCETYPE;
    protected $RESOURCENAME;
    protected $SEP_IBM;
    protected $OCT_IBM;
    protected $NOV_IBM;
    protected $DEC_IBM;
    protected $SEP_TO_DEC_TOTAL_FORECAST;
    protected $SEP_TO_DEC_IBM;
    protected $REMAININGYEARTOTALCY;
    protected $TAXONOMY;
	protected $DIVISION;
	protected $ITDELIVERYDIRECTOR;
	protected $PRNNAME;
	protected $GCMREFERENCE;
	protected $PROJECTPRIORITISATION;
	protected $COMPLEXITYRATING;
	protected $ITPM;
	protected $CLARITY_PM_OWNER;
	protected $DIRECTORATE;
	protected $HOF;
	protected $EMPLOYEETYPE;
	protected $VAT_REDUCTION_APPLIED;
	protected $EMPLOYEENUMBER;
	protected $NEW_FILE_ID;
	protected $AGENCY;
	protected $JOBNAME;
	protected $ALLOCATION;

    static function htmlHeaderRow(){
        ?>
    	<tr>
   		<th>CIO</th>
		<th>PRN</th>
		<th>Project Name</th>
		<th>Project Number</th>
		<th>Current Platform</th>
		<th>Resource Type</th>
		<th>Resource Name</th>
		<th>Sep_IBM</th>
		<th>Oct_IBM</th>
		<th>Nov_IBM</th>
		<th>Dec_IBM</th>
		<th>Sep To Dec Total Forecast</th>
		<th>Sep To Dec IBM</th>
		<th>Remaining Year Total CY</th>
		<th>Taxonomy</th>
		<th>Division</th>
		<th>IT Delivery Director</th>
		<th>PRN Name</th>
		<th>GCM Reference</th>
		<th>Project Prioritisation</th>
		<th>Complexity Rating</th>
		<th>ITPM</th>
		<th>Clarity PM Owner</th>
		<th>Directorate</th>
		<th>HoF</th>
		<th>Employee Type</th>
		<th>VAT Reduction Applied</th>
		<th>Employee Number</th>
		<th>New File Id</th>
		<th>Agency</th>
		<th>Jobname</th>
		<th>Allocation</th>
	   	</tr>
        <?php
    }


}

