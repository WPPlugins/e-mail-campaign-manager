<?php
	if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

	$search = false;
	if (isset($_GET["s"]) && !empty($_GET["s"])) {
		$search = strip_tags($_GET["s"]);
	}

	if (isset($_GET["p"]) && !empty($_GET["p"])) {
		$current_page = intval($_GET["p"]);
	} else {
		$current_page = 1;
	}
?>
<form method="get" action="<?php echo ECM_CURRENT_PATH."&tab=".ECM_CURRENT_TAB; ?>">
<p class="search-box">
	<input type="hidden" name="page" value="<?php echo ECM_PLUGIN_QS_NAME; ?>" />
	<input type="hidden" name="tab" value="<?php echo ECM_CURRENT_TAB; ?>" />

	<label class="screen-reader-text" for="plugin-search-input">Search Users:</label>
	<input type="search" id="plugin-search-input" name="s" value="<?php if ($search) { echo $search; } ?>">
	<input type="submit" id="search-submit" class="button" value="Search users by e-mail"></p>
</form>

<h1>
	Subscribed users
	&nbsp;<a href="<?php echo ECM_CURRENT_PATH; ?>&tab=new-user" class="page-title-action">Add New</a>
	&nbsp;<a href="<?php echo ECM_CURRENT_PATH; ?>&tab=delete-not-activated" class="page-title-action">Delete not activated users</a>
</h1>

<?php
	$on_page = 20;
	$total = count(ecm_get_users($search));
	$data = ecm_get_users($search, $on_page, false, false, $current_page);
?>

<br />
<table class="wp-list-table widefat fixed striped pages">
	<thead>
		<tr>
			<td id="cb" class="manage-column column-cb check-column">
				<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
				<input id="cb-select-all-1" type="checkbox">
			</td>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=name&amp;order=asc">
					<span>Name</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=email&amp;order=asc">
					<span>Email</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=campaign&amp;order=asc">
					<span>Campaign</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=active&amp;order=asc">
					<span>Active</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=dateJoined&amp;order=asc">
					<span>Date Joined</span><span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</thead>

	<?php if ($data == false): ?>
	<tbody id="the-list">
		<tr class="no-items">
			<td class="colspanchange" colspan="6">No users, yet.</td>
		</tr>
	</tbody>
	<?php endif; ?>

	<?php if ($data): ?>
	<tbody id="the-list">
		<?php foreach ($data as $k=>$v): ?>
		<tr id="user-<?php echo $v["userID"]; ?>" class="iedit author-self level-0 status-publish hentry">
			<th scope="row" class="check-column">
				<label class="screen-reader-text" for="cb-select-<?php echo $v["userID"]; ?>">Select <?php echo $v["name"]; ?></label>
				<input id="cb-select-39" type="checkbox" name="post[]" value="<?php echo $v["userID"]; ?>">
				<div class="locked-indicator"></div>
			</th>
			<td class="title column-title has-row-actions column-primary page-title" data-colname="Name">
				<strong><a class="row-title" href="<?php echo ECM_CURRENT_PATH; ?>&tab=edit-user&userID=<?php echo $v["userID"]; ?>" aria-label="“<?php echo $v["name"]; ?>” (Edit)"><?php echo $v["name"]; ?></a></strong>

				<div class="row-actions">
					<span class="edit"><a href="<?php echo ECM_CURRENT_PATH; ?>&tab=edit-user&userID=<?php echo $v["userID"]; ?>" aria-label="Edit “<?php echo $v["name"]; ?>”">Edit</a> | </span>
					<span class="trash"><a href="<?php echo ECM_CURRENT_PATH; ?>&tab=delete-user&userID=<?php echo $v["userID"]; ?>" class="submitdelete" aria-label="Delete user “<?php echo $v["name"]; ?>”">Delete</a></span>
				</div>
				<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
			</td>
			<td class="title column-title has-row-actions column-primary" data-colname="email">
				<?php echo $v["email"]; ?>
			</td>
			<td class="title column-title has-row-actions column-primary" data-colname="campaign-name">
				<?php echo $v["campaignName"]; ?>
			</td>
			<td class="title column-title has-row-actions column-primary" data-colname="active">
				<?php echo (($v["active"] == 1) ? "<strong>Active</strong>" : "Not active"); ?>
			</td>
			<td class="title column-title has-row-actions column-primary" data-colname="date-joined">
				<?php echo $v["dateJoined"]; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	<?php endif; ?>

	<tfoot>
		<tr>
			<td class="manage-column column-cb check-column">
				<label class="screen-reader-text" for="cb-select-all-2">Select All</label>
				<input id="cb-select-all-2" type="checkbox">
			</td>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=name&amp;order=asc">
					<span>Name</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=email&amp;order=asc">
					<span>Email</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=campaign&amp;order=asc">
					<span>Campaign</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=active&amp;order=asc">
					<span>Active</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=dateJoined&amp;order=asc">
					<span>Date Joined</span><span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</tfoot>
</table>

<div class="tablenav bottom">
	<div class="tablenav-pages">
		<?php echo ecm_getPagination($current_page, $total, $on_page, ECM_CURRENT_PATH."&tab=userlist&p=%n%"); ?>
	</div>
</div>
