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

	<label class="screen-reader-text" for="plugin-search-input">Search Campaigns:</label>
	<input type="search" id="plugin-search-input" name="s" value="<?php if ($search) { echo $search; } ?>">
	<input type="submit" id="search-submit" class="button" value="Search campaigns"></p>
</form>

<h1>Your Campaigns &nbsp;<a href="<?php echo ECM_CURRENT_PATH; ?>&tab=new-campaign" class="page-title-action">Add New</a></h1>

<?php
	$on_page = 20;
	$total = count(ecm_get_campaigns($search));
	$data = ecm_get_campaigns($search, $on_page, $current_page);
?>

<br />
<table class="wp-list-table widefat fixed striped pages">
	<thead>
		<tr>
			<td id="cb" class="manage-column column-cb check-column">
				<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
				<input id="cb-select-all-1" type="checkbox">
			</td>
			<th scope="col" id="title" class="manage-column column-title">
				Campaign ID
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=campaignName&amp;order=asc">
					<span>Name</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				Subscribers
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=dateAdded&amp;order=asc">
					<span>Date Added</span><span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</thead>

	<?php if ($data == false): ?>
	<tbody id="the-list">
		<tr class="no-items">
			<td class="colspanchange" colspan="4">No campaigns, yet.</td>
		</tr>
	</tbody>
	<?php endif; ?>

	<?php if ($data): ?>
	<tbody id="the-list">
		<?php foreach ($data as $k=>$v): ?>
		<tr id="campaign-<?php echo $v["campaignID"]; ?>" class="iedit author-self level-0 status-publish hentry">
			<th scope="row" class="check-column">
				<label class="screen-reader-text" for="cb-select-<?php echo $v["campaignID"]; ?>">Select <?php echo $v["campaignName"]; ?></label>
				<input id="cb-select-39" type="checkbox" name="post[]" value="<?php echo $v["campaignID"]; ?>">
				<div class="locked-indicator"></div>
			</th>
			<td class="title column-title has-row-actions column-primary" data-colname="ID">
				<?php echo $v["campaignID"]; ?>
			</td>
			<td class="title column-title has-row-actions column-primary page-title" data-colname="Name">
				<strong><a class="row-title" href="<?php echo ECM_CURRENT_PATH; ?>&tab=edit-campaign&campaignID=<?php echo $v["campaignID"]; ?>" aria-label="“<?php echo $v["campaignName"]; ?>” (Edit)"><?php echo $v["campaignName"]; ?></a></strong>

				<div class="row-actions">
					<span class="edit"><a href="<?php echo ECM_CURRENT_PATH; ?>&tab=edit-campaign&campaignID=<?php echo $v["campaignID"]; ?>" aria-label="Edit “<?php echo $v["name"]; ?>”">Edit</a> | </span>
					<span class="trash"><a href="<?php echo ECM_CURRENT_PATH; ?>&tab=delete-campaign&campaignID=<?php echo $v["campaignID"]; ?>" class="submitdelete" aria-label="Delete campaign “<?php echo $v["campaignName"]; ?>”">Delete</a></span>
				</div>
				<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
			</td>
			<td class="title column-title has-row-actions column-primary" data-colname="subscribers">
				<?php echo $v["subscribers"]; ?>
			</td>
			<td class="title column-title has-row-actions column-primary" data-colname="date-added">
				<?php echo $v["dateAdded"]; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	<?php endif; ?>

	<tfoot>
		<tr>
			<td id="cb" class="manage-column column-cb check-column">
				<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
				<input id="cb-select-all-1" type="checkbox">
			</td>
			<th scope="col" id="title" class="manage-column column-title">
				Campaign ID
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=campaignName&amp;order=asc">
					<span>Name</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				Subscribers
			</th>
			<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
				<a href="<?php echo ECM_CURRENT_PATH; ?>&amp;orderby=dateAdded&amp;order=asc">
					<span>Date Added</span><span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</tfoot>
</table>

<div class="tablenav bottom">
	<div class="tablenav-pages">
		<?php echo ecm_getPagination($current_page, $total, $on_page, ECM_CURRENT_PATH."&tab=campaigns&p=%n%"); ?>
	</div>
</div>