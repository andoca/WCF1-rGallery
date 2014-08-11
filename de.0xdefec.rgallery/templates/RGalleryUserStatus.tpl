<div style="width: 100%; border: 1px solid #ccc; height: 15px;margin-bottom: 4px">
	<div style="width: {$user_status.percent_uploads_per_week}%; background-color: #{$user_status.color_uploads_per_week}; height: 100%">
	</div>
	<div style="margin-top: -18px;padding: 2px; color: #000; width: 100%; text-align: center">
		{if $user_status.left_uploads_per_week == '-1'}
			{lang}de.0xdefec.rgallery.uploads_unlimited{/lang}
		{else}
			{lang}de.0xdefec.rgallery.uploads_left{/lang}
		{/if}
	</div>
</div>
<div style="width: 100%; border: 1px solid #ccc; height: 15px;margin-bottom: 4px">
	<div style="width: {$user_status.percent_quota}%; background-color: #{$user_status.color_quota}; height: 100%">
	</div>
	<div style="margin-top: -18px;padding: 2px; color: #000; width: 100%; text-align: center">
		{if $user_status.left_quota == '-1'}
			{lang}de.0xdefec.rgallery.space_unlimited{/lang}
		{else}
			{lang}de.0xdefec.rgallery.space_left{/lang}
		{/if}
	</div>
</div>
<h3><strong>{lang}de.0xdefec.rgallery.userstats{/lang}</strong></h3>
<ul style="margin:0">
  <li>{lang}de.0xdefec.rgallery.elements{/lang}: {#$user_status.totalitems}</li>
  <li>{lang}de.0xdefec.rgallery.comments{/lang}: {#$user_status.totalcomments}</li>
  <li>{lang}de.0xdefec.rgallery.clicks{/lang}: {#$user_status.totalclicks}</li>
  <li>{lang}de.0xdefec.rgallery.quota_usage{/lang}: {@$user_status.current_quota|filesize}</li>
  {if $user_status.nextupload|isset}<li>{lang}de.0xdefec.rgallery.nextupload{/lang}: {@$user_status.nextupload|time}</li>{/if}
</ul>
