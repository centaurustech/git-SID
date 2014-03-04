var FiltersEnabled = 0; // if your not going to use transitions or filters in any of the tips set this to 0
var spacer="&nbsp; &nbsp; &nbsp; ";

// email notifications to admin
notifyAdminNewMembers0Tip=["", spacer+"No email notifications to admin."];
notifyAdminNewMembers1Tip=["", spacer+"Notify admin only when a new member is waiting for approval."];
notifyAdminNewMembers2Tip=["", spacer+"Notify admin for all new sign-ups."];

// visitorSignup
visitorSignup0Tip=["", spacer+"If this option is selected, visitors will not be able to join this group unless the admin manually moves them to this group from the admin area."];
visitorSignup1Tip=["", spacer+"If this option is selected, visitors can join this group but will not be able to sign in unless the admin approves them from the admin area."];
visitorSignup2Tip=["", spacer+"If this option is selected, visitors can join this group and will be able to sign in instantly with no need for admin approval."];

// clients table
clients_addTip=["",spacer+"This option allows all members of the group to add records to the 'Clients' table. A member who adds a record to the table becomes the 'owner' of that record."];

clients_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Clients' table."];
clients_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Clients' table."];
clients_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Clients' table."];
clients_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Clients' table."];

clients_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Clients' table."];
clients_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Clients' table."];
clients_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Clients' table."];
clients_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Clients' table, regardless of their owner."];

clients_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Clients' table."];
clients_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Clients' table."];
clients_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Clients' table."];
clients_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Clients' table."];

// companies table
companies_addTip=["",spacer+"This option allows all members of the group to add records to the 'Companies' table. A member who adds a record to the table becomes the 'owner' of that record."];

companies_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Companies' table."];
companies_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Companies' table."];
companies_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Companies' table."];
companies_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Companies' table."];

companies_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Companies' table."];
companies_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Companies' table."];
companies_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Companies' table."];
companies_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Companies' table, regardless of their owner."];

companies_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Companies' table."];
companies_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Companies' table."];
companies_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Companies' table."];
companies_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Companies' table."];

// sic table
sic_addTip=["",spacer+"This option allows all members of the group to add records to the 'SIC' table. A member who adds a record to the table becomes the 'owner' of that record."];

sic_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'SIC' table."];
sic_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'SIC' table."];
sic_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'SIC' table."];
sic_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'SIC' table."];

sic_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'SIC' table."];
sic_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'SIC' table."];
sic_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'SIC' table."];
sic_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'SIC' table, regardless of their owner."];

sic_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'SIC' table."];
sic_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'SIC' table."];
sic_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'SIC' table."];
sic_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'SIC' table."];

// reports table
reports_addTip=["",spacer+"This option allows all members of the group to add records to the 'Reports' table. A member who adds a record to the table becomes the 'owner' of that record."];

reports_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Reports' table."];
reports_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Reports' table."];
reports_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Reports' table."];
reports_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Reports' table."];

reports_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Reports' table."];
reports_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Reports' table."];
reports_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Reports' table."];
reports_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Reports' table, regardless of their owner."];

reports_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Reports' table."];
reports_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Reports' table."];
reports_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Reports' table."];
reports_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Reports' table."];

// entries table
entries_addTip=["",spacer+"This option allows all members of the group to add records to the 'Entries' table. A member who adds a record to the table becomes the 'owner' of that record."];

entries_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Entries' table."];
entries_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Entries' table."];
entries_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Entries' table."];
entries_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Entries' table."];

entries_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Entries' table."];
entries_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Entries' table."];
entries_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Entries' table."];
entries_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Entries' table, regardless of their owner."];

entries_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Entries' table."];
entries_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Entries' table."];
entries_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Entries' table."];
entries_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Entries' table."];

// outcome_areas table
outcome_areas_addTip=["",spacer+"This option allows all members of the group to add records to the 'Outcome areas' table. A member who adds a record to the table becomes the 'owner' of that record."];

outcome_areas_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Outcome areas' table."];
outcome_areas_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Outcome areas' table."];
outcome_areas_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Outcome areas' table."];
outcome_areas_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Outcome areas' table."];

outcome_areas_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Outcome areas' table."];
outcome_areas_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Outcome areas' table."];
outcome_areas_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Outcome areas' table."];
outcome_areas_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Outcome areas' table, regardless of their owner."];

outcome_areas_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Outcome areas' table."];
outcome_areas_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Outcome areas' table."];
outcome_areas_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Outcome areas' table."];
outcome_areas_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Outcome areas' table."];

// outcomes table
outcomes_addTip=["",spacer+"This option allows all members of the group to add records to the 'Outcomes' table. A member who adds a record to the table becomes the 'owner' of that record."];

outcomes_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Outcomes' table."];
outcomes_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Outcomes' table."];
outcomes_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Outcomes' table."];
outcomes_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Outcomes' table."];

outcomes_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Outcomes' table."];
outcomes_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Outcomes' table."];
outcomes_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Outcomes' table."];
outcomes_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Outcomes' table, regardless of their owner."];

outcomes_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Outcomes' table."];
outcomes_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Outcomes' table."];
outcomes_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Outcomes' table."];
outcomes_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Outcomes' table."];

// beneficiary_groups table
beneficiary_groups_addTip=["",spacer+"This option allows all members of the group to add records to the 'Beneficiary groups' table. A member who adds a record to the table becomes the 'owner' of that record."];

beneficiary_groups_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Beneficiary groups' table."];
beneficiary_groups_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Beneficiary groups' table."];
beneficiary_groups_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Beneficiary groups' table."];
beneficiary_groups_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Beneficiary groups' table."];

beneficiary_groups_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Beneficiary groups' table."];
beneficiary_groups_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Beneficiary groups' table."];
beneficiary_groups_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Beneficiary groups' table."];
beneficiary_groups_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Beneficiary groups' table, regardless of their owner."];

beneficiary_groups_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Beneficiary groups' table."];
beneficiary_groups_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Beneficiary groups' table."];
beneficiary_groups_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Beneficiary groups' table."];
beneficiary_groups_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Beneficiary groups' table."];

// indicators table
indicators_addTip=["",spacer+"This option allows all members of the group to add records to the 'Indicators' table. A member who adds a record to the table becomes the 'owner' of that record."];

indicators_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Indicators' table."];
indicators_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Indicators' table."];
indicators_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Indicators' table."];
indicators_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Indicators' table."];

indicators_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Indicators' table."];
indicators_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Indicators' table."];
indicators_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Indicators' table."];
indicators_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Indicators' table, regardless of their owner."];

indicators_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Indicators' table."];
indicators_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Indicators' table."];
indicators_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Indicators' table."];
indicators_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Indicators' table."];

/*
	Style syntax:
	-------------
	[TitleColor,TextColor,TitleBgColor,TextBgColor,TitleBgImag,TextBgImag,TitleTextAlign,
	TextTextAlign,TitleFontFace,TextFontFace, TipPosition, StickyStyle, TitleFontSize,
	TextFontSize, Width, Height, BorderSize, PadTextArea, CoordinateX , CoordinateY,
	TransitionNumber, TransitionDuration, TransparencyLevel ,ShadowType, ShadowColor]

*/

toolTipStyle=["white","#00008B","#000099","#E6E6FA","","images/helpBg.gif","","","","\"Trebuchet MS\", sans-serif","","","","3",400,"",1,2,10,10,51,1,0,"",""];

applyCssFilter();
