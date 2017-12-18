<?php
function fed_initial_setup() {
	if($_POST && $_POST['slug']){
		include(ABSPATH .'wp-admin/includes/ajax-actions.php');
		wp_ajax_install_plugin();
	}

	?>
	<form method="post">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Slug</label>
			<div class="col-sm-10">
				<input type="text" name="slug" class="form-control" placeholder="">
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>

		</div>
	</form>

	<div class="fed_initial_setup_container">
		<div class="bc_fed container">
		<div class="row">
			<div class="col-md-12">
				<div class="pull-right">
					<button class="btn btn-primary fed_initial_setup_close">X</button>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="progress" id="progress1">
				<div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
				</div>
				<span class="progress-type">Overall Progress</span>
				<span class="progress-completed">20%</span>
			</div>
		</div>
		<div class="row">
			<div class="row step">
				<div id="div1" class="col-md-2" onclick="javascript: resetActive(event, 0, 'step-1');">
					<span class="fa fa-cloud-download"></span>
					<p>Download Application</p>
				</div>
				<div class="col-md-2 activestep" onclick="javascript: resetActive(event, 20, 'step-2');">
					<span class="fa fa-pencil"></span>
					<p>Fill out</p>
				</div>
				<div class="col-md-2" onclick="javascript: resetActive(event, 40, 'step-3');">
					<span class="fa fa-refresh"></span>
					<p>Check</p>
				</div>
				<div class="col-md-2" onclick="javascript: resetActive(event, 60, 'step-4');">
					<span class="fa fa-dollar"></span>
					<p>Pay Fee</p>
				</div>
				<div class="col-md-2" onclick="javascript: resetActive(event, 80, 'step-5');">
					<span class="fa fa-cloud-upload"></span>
					<p>Submit Application</p>
				</div>
				<div id="last" class="col-md-2" onclick="javascript: resetActive(event, 100, 'step-6');">
					<span class="fa fa-star"></span>
					<p>Send Feedback</p>
				</div>
			</div>
		</div>
		<div class="row setup-content step hiddenStepInfo" id="step-1">
			<div class="col-xs-12">
				<div class="col-md-12 well text-center">
					<h1>STEP 1</h1>
					<h3 class="underline">Instructions</h3>
					Download the application form from our repository.
					This may require logging in.
				</div>
			</div>
		</div>
		<div class="row setup-content step activeStepInfo" id="step-2">
			<div class="col-xs-12">
				<div class="col-md-12 well text-center">
					<h1>STEP 2</h1>
					<h3 class="underline">Instructions</h3>
					Fill out the application.
					Make sure to leave no empty fields.
				</div>
			</div>
		</div>
		<div class="row setup-content step hiddenStepInfo" id="step-3">
			<div class="col-xs-12">
				<div class="col-md-12 well text-center">
					<h1>STEP 3</h1>
					<h3 class="underline">Instructions</h3>
					Check to ensure that all data entered is valid.
				</div>
			</div>
		</div>
		<div class="row setup-content step hiddenStepInfo" id="step-4">
			<div class="col-xs-12">
				<div class="col-md-12 well text-center">
					<h1>STEP 4</h1>
					<h3 class="underline">Instructions</h3>
					Pay the application fee.
					This can be done either by entering your card details, or by using Paypal.
				</div>
			</div>
		</div>
		<div class="row setup-content step hiddenStepInfo" id="step-5">
			<div class="col-xs-12">
				<div class="col-md-12 well text-center">
					<h1>STEP 5</h1>
					<h3 class="underline">Instructions</h3>
					Upload the application.
					This may require a confirmation email.
				</div>
			</div>
		</div>
		<div class="row setup-content step hiddenStepInfo" id="step-6">
			<div class="col-xs-12">
				<div class="col-md-12 well text-center">
					<h1>STEP 6</h1>
					<h3 class="underline">Instructions</h3>
					Send us feedback on the overall process.
					This step is not obligatory.
				</div>
			</div>
		</div>
		</div>
	</div>

	<style>
		.hiddenStepInfo {
			display: none;
		}

		.activeStepInfo {
			display: block !important;
		}

		.underline {
			text-decoration: underline;
		}

		.step {
			margin-top: 27px;
		}

		.progress {
			position: relative;
			height: 25px;
		}

		.progress > .progress-type {
			position: absolute;
			left: 0;
			font-weight: 800;
			padding: 3px 30px 2px 10px;
			color: rgb(255, 255, 255);
			background-color: rgba(25, 25, 25, 0.2);
		}

		.progress > .progress-completed {
			position: absolute;
			right: 0;
			font-weight: 800;
			padding: 3px 10px 2px;
		}

		.step {
			text-align: center;

		}

		.step .col-md-2 {
			background-color: #033333;
			border-right: none;
			color: #ffffff;
		}

		/*.step .col-md-2:last-child {*/
			/*border: 1px solid #C0C0C0;*/
		/*}*/

		/*.step .col-md-2:first-child {*/
			/*border-radius: 5px 0 0 5px;*/
		/*}*/

		/*.step .col-md-2:last-child {*/
			/*border-radius: 0 5px 5px 0;*/
		/*}*/

		.step .col-md-2:hover {
			background: #F3F3F3;
			color: #033333;
			cursor: pointer;
		}

		.step .activestep {
			color: #ffffff;
			height: 100px;
			margin-top: -7px;
			padding-top: 7px;
			background: #0AAAAA;
			vertical-align: central;
		}

		.step .fa {
			padding-top: 15px;
			font-size: 40px;
		}
	</style>

	<script type="text/javascript">
        function resetActive(event, percent, step) {
            $(".progress-bar").css("width", percent + "%").attr("aria-valuenow", percent);
            $(".progress-completed").text(percent + "%");

            $("div").each(function () {
                if ($(this).hasClass("activestep")) {
                    $(this).removeClass("activestep");
                }
            });

            if (event.target.className == "col-md-2") {
                $(event.target).addClass("activestep");
            }
            else {
                $(event.target.parentNode).addClass("activestep");
            }

            hideSteps();
            showCurrentStepInfo(step);
        }

        function hideSteps() {
            $("div").each(function () {
                if ($(this).hasClass("activeStepInfo")) {
                    $(this).removeClass("activeStepInfo");
                    $(this).addClass("hiddenStepInfo");
                }
            });
        }

        function showCurrentStepInfo(step) {
            var id = "#" + step;
            $(id).addClass("activeStepInfo");
        }
	</script>
<?php } ?>