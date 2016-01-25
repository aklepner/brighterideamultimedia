<!--
	Purpose:
			Workout the paths needed for the Magento indexing cron jobs 
	Created by: 
			Matthew Ogborne, UnderstandingE.com
	Version: 
			1.0
-->
<h2>Create Cron Jobs for Reindexing Magento Helper Script</h2>
<p>This script will generate the paths you need to set up the reindexing schedules for Magento</p>

<h3>Stock &amp; Prices</h3>
<p>Copy &amp; paste this line of code for your stock &amp; prices cron job and set this to run <b>every hour at 5 minutes past</b> each day.</p>

<textarea rows="3" cols="100">
php <?php echo realpath(dirname(__FILE__)); ?>/shell/indexer.php --reindex catalog_product_price,cataloginventory_stock &gt; /dev/null 2&gt;&amp;1
</textarea>

<p>Your cron job should look similar to this (Click image for larger view):
<br /><br />
<a href="http://understandinge.com/wp-content/uploads/2014/03/cron_job_every_hour_at_5_past.png" target="_blank">
<img src="http://understandinge.com/wp-content/uploads/2014/03/cron_job_every_hour_at_5_past.png" width="400" />
</a>
</p>

<h3>Full Reindex</h3>
<p>Copy &amp; paste this line of code for your full reindex cron job and set this to run <b> at 02:25 in the morning</b> every day.</p>

<textarea rows="3" cols="100">
php <?php echo realpath(dirname(__FILE__)); ?>/shell/indexer.php --reindexall &gt; /dev/null 2&gt;&amp;1
</textarea>

<p>Your cron job should look similar to this (Click image for larger view):
<br /><br />
<a href="http://understandinge.com/wp-content/uploads/2014/03/cron_job_0220.png" target="_blank">
<img src="http://understandinge.com/wp-content/uploads/2014/03/cron_job_0220.png" width="400" />
</a>
</p>

<h3>Need Help?</h3>
<p>If you need a hand, the UnderstandingE forums are always open: <a href="http://understandinge.com/forum/" target="_blank">http://understandinge.com/forum/</a></p>
<br /><br />
<br /><br />
<br /><br />
<br /><br />