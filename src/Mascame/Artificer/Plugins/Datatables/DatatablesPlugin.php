<?php namespace Mascame\Artificer\Plugins\Datatables;

use Mascame\Artificer\Artificer;
use Mascame\Artificer\Plugin\Plugin;
use Event;

class DatatablesPlugin extends Plugin {

	public function meta()
	{
		$this->version = '1.0';
		$this->name = 'Datatables';
		$this->description = 'Datatables for models';
		$this->author = 'Marc Mascarell';
	}

	public function boot()
	{
		$this->addHooks();
	}

	public function addHooks()
	{
        $this->addHeadStylesListener();
		$this->addHeadScriptsListener();
	}

    public function addHeadStylesListener() {
        Event::listen(array('artificer.view.head-styles'), function () {
            ?>
            <!-- DATA TABLES -->
            <link
                href="<?php print asset('packages/mascame/artificer/plugins/mascame/datatables/dataTables.bootstrap.css') ?>"
                rel="stylesheet" type="text/css"/>
        <?php
        });
    }

    public function addHeadScriptsListener() {
        Event::listen(array('artificer.view.head-scripts'), function () {
            ?>
            <!-- DATA TABES SCRIPT -->
            <script
                src="<?php print asset('packages/mascame/artificer/plugins/mascame/datatables/jquery.dataTables.js') ?>"
                type="text/javascript"></script>
            <script
                src="<?php print asset('packages/mascame/artificer/plugins/mascame/datatables/dataTables.bootstrap.js') ?>"
                type="text/javascript"></script>

            <!-- page script -->
            <script type="text/javascript">
                $(function () {
                    $('.datatable').dataTable({
                        "bPaginate": true,
                        "bLengthChange": true,
                        "bFilter": true,
                        "bSort": true,
                        "bInfo": true,
                        "bAutoWidth": false
                    });
                });
            </script>
        <?php
        });
    }

}