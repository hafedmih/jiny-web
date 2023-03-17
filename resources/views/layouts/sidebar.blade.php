<style>
	.nav{
		flex-wrap: inherit;
	}
	 .nav-group-sub{
		background-color: #3d4252 !important;
		color: #ffff !important;
		
	}
	
	
</style>
<!-- Sidebar mobile toggler -->
<div class="sidebar-mobile-toggler text-center">
	<a href="#" class="sidebar-mobile-main-toggle">
		<i class="icon-arrow-left8"></i>
	</a>
	<span class="font-weight-semibold">Navigation</span>
	<a href="#" class="sidebar-mobile-expand">
		<i class="icon-screen-full"></i>
		<i class="icon-screen-normal"></i>
	</a>
</div>
		
<div class="sidebar-content">

	<div class="sidebar-user-material">
		<div class="sidebar-user-material-body">
			<div class="card-body text-center">
			<a href="#">
					<img src="{{auth()->user()->profile_pic ? auth()->user()->profile_pic : asset('backend/face6.jpg')}}" class="img-fluid rounded-circle shadow-1 mb-3" width="80" height="80" alt="">
				</a>
				<h6 class="mb-0 text-white text-shadow-dark">{{ auth()->user()->firstname }}</h6>
				<span class="font-size-sm text-white text-shadow-dark">{{ auth()->user()->email }}</span>
			</div>
										
			<div class="sidebar-user-material-footer">
				<a href="#user-nav" class="d-flex justify-content-between align-items-center text-shadow-dark dropdown-toggle" data-toggle="collapse"><span>My account</span></a>
			</div>
		</div>
		<div class="collapse" id="user-nav">
			<ul class="nav nav-sidebar">
			@if(auth()->user()->can('profile-bar'))
				<li class="nav-item">
					<a href="{{ route('profile') }}" class="nav-link">
						<i class="icon-user-plus"></i>
						<span>My profile</span>
					</a>
				</li>
			@endif
				@if(auth()->user()->can('account-settings-bar'))
				<li class="nav-item">
					<a href="{{route('settings')}}" class="nav-link">
						<i class="icon-cog5"></i>
						<span>Account settings</span>
					</a>
				</li>
				@endif
			
			</ul>
		</div>
	</div>
	<!-- /user menu -->

	<!-- Main navigation -->
	<div class="card card-sidebar-mobile">
		<ul class="nav nav-sidebar" data-nav-type="accordion">

			<!-- Main -->
			<li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs"> {{ __('main') }}  </div> <i class="icon-menu" title="Main"></i></li>
			@if(auth()->user()->can('dashboard-bar'))
			<li class="nav-item">
				<a href="{{route('dashboard')}}" class="nav-link ">
					<i class="icon-home"></i>
					<span>
						{{ __('dashboard') }}
					</span>
				</a>
			</li>
			@endif
			@if(auth()->user()->can('user-management-bar'))
			<li class="nav-item nav-item-submenu">
				<a href="#" class="nav-link"><i class="icon-user"></i> <span>{{ __('user_management') }}</span></a>
				<ul class="nav nav-group-sub  " data-submenu-title="{{ __('user_management') }}" >
					@if(auth()->user()->can('admin-bar'))
					<li class="nav-item">
						<a href="{{ route('user') }}" class="nav-link"><i class="icon-user-tie"></i> <span>{{ __('admin') }}</span></a>
					</li>
					@endif
					@if(auth()->user()->can('user-bar'))
					<li class="nav-item">
						<a href="{{ route('userManage') }}" class="nav-link"><i class="icon-users"></i> <span>{{ __('user') }}</span></a>
					</li>
					@endif

					@if(auth()->user()->can('driver-bar')) 
					<li class="nav-item">
						<a href="{{ route('driver') }}" class="nav-link"><i class="icon-steering-wheel"></i> <span>{{ __('driver') }}</span></a>
					</li>
					@endif	
				</ul>	
			</li>
			@endif
			@if(auth()->user()->can('dispatcher-bar'))
			<li class="nav-item nav-item-submenu">
				<a href="#" class="nav-link"><i class="icon-truck"></i> <span>{{ __('dispatcher') }}</span></a>
				<ul class="nav nav-group-sub" data-submenu-title="{{ __('dispatcher') }}">
					@if(auth()->user()->can('dispatcher-request-bar'))
					<li class="nav-item">
						<a href="{{ route('dispatcher') }}" class="nav-link"><i class="icon-map4"></i> <span>{{ __('new-request') }}</span></a>
					</li>
					@endif
					
					@if(auth()->user()->can('dispatcher-history-bar'))
					<li class="nav-item">
						<a href="{{ route('dispatcherTripList') }}" class="nav-link"><i class="icon-clipboard6"></i> <span>{{ __('history') }}</span></a>
					</li>
					@endif
				</ul>
			</li>
			@endif
<!-- 
			<li class="nav-item nav-item-submenu">
				<a href="#" class="nav-link"><i class="icon-users"></i> <span>{{ __('driver-management') }}</span></a>
				<ul class="nav nav-group-sub" data-submenu-title="JSON forms">
					<li class="nav-item"><a href="{{ route('driver') }}" class="nav-link">{{ __('manage-driver') }}</a></li>
				
				</ul>
			</li> -->

			<li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs"> {{ __('taxi') }}  </div> <i class="icon-menu" title="Main"></i></li>
            @if(auth()->user()->can('request-bar'))
			<li class="nav-item nav-item-submenu">
				<a href="#" class="nav-link"><i class="icon-clipboard3"></i> <span>{{ __('request-management') }}</span></a>
				<ul class="nav nav-group-sub" data-submenu-title="{{ __('request-management') }}">
					@if(auth()->user()->can('request-list-bar'))
						<li class="nav-item">
							<a href="{{route('request')}}" class="nav-link ">
								<i class="icon-list3"></i>
								<span>{{ __('request-list') }}</span>
							</a>
						</li>
					@endif
					
				</ul>
			</li>
			@endif
            <!-- @if(auth()->user()->can('outstation-bar'))
			<li class="nav-item nav-item-submenu">
				<a href="#" class="nav-link"><i class="icon-car2"></i> <span>{{ __('outstation') }}</span></a>
				<ul class="nav nav-group-sub" data-submenu-title="{{ __('outstation') }}">
					@if(auth()->user()->can('outstation-master-bar'))	
						<li class="nav-item"><a href="{{route('out-station')}}" class="nav-link "><i class="icon-location4"></i>{{ __('master') }}</a></li>
					@endif
					@if(auth()->user()->can('outstation-setprice-bar'))	
						<li class="nav-item"><a href="{{route('outstationSetPrice')}}" class="nav-link "><i class="icon-cash2"></i>{{ __('set-price') }}</a></li>
					@endif
					@if(auth()->user()->can('outstation-package-bar'))	
						<li class="nav-item"><a href="{{route('outstation-package')}}" class="nav-link "><i class="icon-package"></i>{{ __('outstation_package') }}</a></li>
					@endif
					@if(auth()->user()->can('outstation-list'))	
						<li class="nav-item"><a href="{{route('outstationTriplist')}}" class="nav-link "><i class="icon-stack-text"></i>{{ __('outstation_list') }}</a></li>
					@endif
				</ul>
			</li>
			@endif	 -->
            @if(auth()->user()->can('zone-management-bar'))
			<li class="nav-item nav-item-submenu">
				<a href="#" class="nav-link"><i class="icon-map"></i> <span>{{ __('zone-management') }}</span></a>
				<ul class="nav nav-group-sub" data-submenu-title="{{ __('zone-management') }}">
					@if(auth()->user()->can('zone-bar'))
						<li class="nav-item"><a href="{{route('zone')}}" class="nav-link "><i class="icon-map"></i>{{ __('zone') }}</a></li>
					@endif
					@if(auth()->user()->can('outofzone-bar'))	
							<li class="nav-item"><a href="{{route('outofzone-master')}}" class="nav-link "><i class="icon-location4"></i>{{ __('out-of-zone-pricing') }}</a></li>
					@endif
				</ul>
			</li>
			@endif
			@if(auth()->user()->can('masters-bar'))
				<li class="nav-item nav-item-submenu">
					<a href="#" class="nav-link"><i class="icon-graduation2"></i> <span>{{ __('masters') }}</span></a>
					<ul class="nav nav-group-sub" data-submenu-title="{{ __('masters') }}">
						@if(auth()->user()->can('masters-category-bar'))	
							<li class="nav-item"><a href="{{route('category')}}" class="nav-link "><i class="icon-cabinet"></i>{{ __('category') }}</a></li>
						@endif		
						@if(auth()->user()->can('masters-vehicle-bar'))
							<li class="nav-item"><a href="{{ route('vehicle') }}" class="nav-link"><i class="icon-car"></i>{{ __('vehicle') }}</a></li>
						@endif	
						@if(auth()->user()->can('masters-vehiclemodel-bar'))
							<li class="nav-item"><a href="{{ route('vehiclemodel') }}" class="nav-link"><i class="icon-car"></i>{{ __('vehicle-model') }}</a></li>
						@endif
						
						@if(auth()->user()->can('masters-group-documents-bar'))
							<li class="nav-item"><a href="{{route('group-documents')}}" class="nav-link "><i class="icon-books"></i>{{ __('group-documents') }}</a></li>	
						@endif
						@if(auth()->user()->can('masters-document-bar'))
							<li class="nav-item"><a href="{{route('documents')}}" class="nav-link "><i class="icon-file-text"></i>{{ __('document') }}</a></li>	
						@endif
					</ul>
				</li>
			@endif

			@if(auth()->user()->can('support-bar'))
				<li class="nav-item nav-item-submenu">
					<a href="#" class="nav-link"><i class="icon-comment-discussion "></i> <span>{{ __('support') }}</span></a>
					<ul class="nav nav-group-sub" data-submenu-title="{{ __('support') }}">
					    @if(auth()->user()->can('trip-complaint-bar'))
							<li class="nav-item"><a href="{{ route('tripComplaint') }}" class="nav-link"><i class="icon-hammer-wrench"></i>{{ __('trip-complaint') }}</a></li>
						@endif	
						@if(auth()->user()->can('support-complaint-bar'))
							<li class="nav-item"><a href="{{ route('complaints') }}" class="nav-link"><i class="icon-cogs"></i>{{ __('manage-complaint') }}</a></li>
						@endif	
						@if(auth()->user()->can('support-complaintlist-bar'))
							<li class="nav-item"><a href="{{ route('userComplaints') }}" class="nav-link"><i class="icon-user-check"></i>{{ __('complaint-list') }}</a></li>
						@endif	
							<!-- <li class="nav-item"><a href="{{ route('complaints') }}" class="nav-link">{{ __('manage-driver-complaint') }}</a></li> -->
						<!-- @if(auth()->user()->can('support-chat-bar'))	
							<li class="nav-item"></i><a href="{{ route('roles.index') }}" class="nav-link"><i class="icon-comment-discussion"></i>{{ __('chat') }}</a></li>
						@endif	 -->
						@if(auth()->user()->can('support-faq-bar'))
							<li class="nav-item"><a href="{{ route('faq-management') }}" class="nav-link"><i class="icon-stack-text"></i>{{ __('faq') }}</a></li>
						@endif	
						@if(auth()->user()->can('support-sos-bar'))
							<li class="nav-item"><a href="{{ route('sos-management') }}" class="nav-link"><i class="icon-vcard"></i>{{ __('sos') }}</a></li>
						@endif
						@if(auth()->user()->can('support-cancelreason-bar'))	
							<li class="nav-item"><a href="{{route('cancellationReason')}}" class="nav-link "><i class="icon-stack"></i>{{ __('cancellation-reasons') }}</a></li>
						@endif	
					</ul>
				</li>
			@endif

            
			@if(auth()->user()->can('benefits-bar'))
				<li class="nav-item nav-item-submenu">
					<a href="#" class="nav-link"><i class="icon-medal-star"></i> <span>{{ __('benefits') }}</span></a>
					<ul class="nav nav-group-sub" data-submenu-title="{{ __('benefits') }}">
						
						@if(auth()->user()->can('benefits-promo'))
							<li class="nav-item"><a href="{{route('promolist')}}" class="nav-link "><i class="icon-file-check2"></i>{{ __('promo-management') }}</a></li>
						@endif		
						
					</ul>
				</li>
			@endif
            @if(auth()->user()->can('promotion'))
			<li class="nav-item nav-item-submenu">
				<a href="#" class="nav-link"><i class="icon-megaphone"></i> <span>{{ __('promotion') }}</span></a>
				<ul class="nav nav-group-sub" data-submenu-title="{{ __('promotion') }}">	
					@if(auth()->user()->can('promotion-notification'))
						<li class="nav-item"><a href="{{ route('notification') }}" class="nav-link"><i class="icon-bubble-notification"></i>{{ __('notification-management') }}</a></li>
					@endif	
					@if(auth()->user()->can('promotion-email'))
						<li class="nav-item"><a href="{{ route('email')}}" class="nav-link"><i class="icon-bubble-notification"></i>{{ __('Email') }}</a></li>
					@endif	
					@if(auth()->user()->can('promotion-sms'))
						<li class="nav-item"><a href="{{ route('sms')}}" class="nav-link"><i class="icon-bubble-notification"></i>{{ __('sms') }}</a></li>
					@endif
				</ul>
			</li>
			@endif
			
			@if(auth()->user()->can('reports-bar'))
				
				<li class="nav-item nav-item-submenu">
					<a href="#" class="nav-link"><i class="icon-medal-star"></i> <span>{{ __('reports') }}</span></a>
					<ul class="nav nav-group-sub" data-submenu-title="{{ __('reports') }}">
					@if(auth()->user()->can('reports-driver'))
						<li class="nav-item"><a href="{{ route('SummaryView') }}" class="nav-link"><i class="icon-car2"></i>{{ __('driver-summary') }}</a></li>
						@endif
						@if(auth()->user()->can('reports-driver'))
						<li class="nav-item"><a href="{{ route('CompletedLocalView') }}" class="nav-link"><i class="icon-car2"></i>{{ __('completed-local-view') }}</a></li>
						@endif
						<!-- @if(auth()->user()->can('reports-driver'))
						<li class="nav-item"><a href="{{ route('CompletedRentalView') }}" class="nav-link"><i class="icon-car2"></i>{{ __('completed-rental-view') }}</a></li>
						@endif
						@if(auth()->user()->can('reports-driver'))
						<li class="nav-item"><a href="{{ route('CompletedOutstationView') }}" class="nav-link"><i class="icon-car2"></i>{{ __('completed-outstation-view') }}</a></li>
						@endif -->
						@if(auth()->user()->can('reports-driver'))
						<li class="nav-item"><a href="{{ route('showreports') }}" class="nav-link"><i class="icon-car2"></i>{{ __('driver-report') }}</a></li>
						@endif	
						@if(auth()->user()->can('user-reports'))
							<li class="nav-item"><a href="{{route('userReports')}}" class="nav-link "><i class="icon-users4"></i>{{ __('user_reports') }}</a></li>
						@endif
						@if(auth()->user()->can('reports-trip'))
							<li class="nav-item"><a href="{{route('showtripReports')}}" class="nav-link "><i class="icon-file-check2"></i>{{ __('trip_reports') }}</a></li>
						@endif	
						@if(auth()->user()->can('trip_wise_reports'))
							<li class="nav-item"><a href="{{route('tripWiseReports')}}" class="nav-link "><i class="icon-file-check2"></i>{{ __('trip_wise_reports') }}</a></li>
						@endif
						@if(auth()->user()->can('transaction'))
							<li class="nav-item"><a href="{{route('transactionReportslist')}}" class="nav-link "><i class="icon-cash3"></i>{{ __('transaction') }}</a></li>
						@endif	
						@if(auth()->user()->can('reports-driver'))
							<li class="nav-item"><a href="{{route('driverWallet')}}" class="nav-link "><i class="icon-wallet"></i>{{ __('driver_wallet') }}</a></li>
						@endif
						@if(auth()->user()->can('questions-reports'))
							<li class="nav-item"><a href="{{route('questionsReports')}}" class="nav-link "><i class="icon-bubbles4"></i>{{ __('questions_reports') }}</a></li>
						@endif
						@if(auth()->user()->can('income-reports'))
							<li class="nav-item"><a href="{{route('totalincomeReports')}}" class="nav-link "><i class="icon-coin-dollar"></i>{{ __('income_reports') }}</a></li>
						@endif
						
						@if(auth()->user()->can('user-reports'))
							<li class="nav-item"><a href="{{route('promoUseList')}}" class="nav-link "><i class="icon-align-bottom"></i>{{ __('promo_use_details') }}</a></li>
						@endif
					</ul>
				</li>
			
			@endif
			@if(auth()->user()->can('fare-amount-details'))
				<li class="nav-item">
					<a href="{{ route('viewFareAmount') }}" class="nav-link">
						<i class="icon-car2"></i> 
						<span>
							{{ __('fare-amount-details') }}
						</span>
					</a>
				</li>
			@endif
			@if(auth()->user()->can('invoice_questions'))
				<li class="nav-item">
					<a href="{{ route('questions-add') }}" class="nav-link">
						<i class="icon-bubbles"></i> 
						<span>
							{{ __('invoice_questions') }}
						</span>
					</a>
				</li>
			@endif

			@if(auth()->user()->can('invoice_questions'))
				<li class="nav-item">
					<a href="{{ route('documentExpiry') }}" class="nav-link">
						<i class="icon-bubbles"></i> 
						<span>
							{{ __('document_expiry_soon') }}
						</span>
					</a>
				</li>
			@endif


			<li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs"> {{ __('others') }}  </div> <i class="icon-menu" title="Main"></i></li>
			
			@if(auth()->user()->can('others-bar'))
				<li class="nav-item nav-item-submenu">
					<a href="#" class="nav-link"><i class="icon-dice"></i> <span>{{ __('others') }}</span></a>
					<ul class="nav nav-group-sub" data-submenu-title="{{ __('others') }}">
						@if(auth()->user()->can('others-privilege'))
							<li class="nav-item nav-item-submenu">
								<a href="#" class="nav-link"><i class="icon-menu-close2"></i>{{ __('privilege-management') }}</a>
								<ul class="nav nav-group-sub">
								@if(auth()->user()->can('others-privilege-roles'))
									<li class="nav-item"><a href="{{ route('roles.index') }}" class="nav-link"><i class="icon-yelp "></i>{{ __('roles') }}</a></li>
								@endif
								@if(auth()->user()->can('others-privilege-permissions'))	
									<li class="nav-item"><a href="{{ route('permissions.index') }}" class="nav-link"><i class="icon-spinner4"></i>{{ __('permission') }}</a></li>
								@endif		
								</ul>
							</li>
						@endif	
						@if(auth()->user()->can('others-language'))
						    <li class="nav-item"><a href="{{ route('viewLanguage') }}" class="nav-link"><i class="icon-newspaper"></i>{{ __('language-master') }}</a></li>
						@endif
						@if(auth()->user()->can('others-country'))
						    <li class="nav-item"><a href="{{ route('country') }}" class="nav-link"><i class="icon-earth"></i>{{ __('country-master') }}</a></li>
						@endif
						@if(auth()->user()->can('others-translation'))
						    <li class="nav-item"><a href="{{ route('languages') }}" class="nav-link"><i class="icon-quill4"></i>{{ __('languages') }}</a></li>
						@endif
						@if(auth()->user()->can('others-projectversion'))
						    <li class="nav-item"><a href="{{ route('versions.index') }}" class="nav-link"><i class="icon-stack-check"></i>{{ __('project-version') }}</a></li>
						@endif
						@if(auth()->user()->can('others-pushtranslation'))
						    <li class="nav-item"><a href="{{ route('push-transaltion-list') }}" class="nav-link"><i class="icon-transmission"></i>{{ __('push_translation') }}</a></li>
						@endif
						@if(auth()->user()->can('others-error_log'))
							<li class="nav-item"><a href="{{route('loglist')}}" class="nav-link "><i class="icon-warning"></i>{{ __('error_log') }}</a></li>
						@endif
						@if(auth()->user()->can('others-reference'))
							<li class="nav-item"><a href="{{route('reference')}}" class="nav-link "><i class="icon-book"></i>{{ __('reference') }}</a></li>
						@endif
						@if(auth()->user()->can('others-heat_map'))
							<li class="nav-item"><a href="{{route('heatmap')}}" class="nav-link "><i class="icon-map"></i>{{ __('heat_map') }}</a></li>
						@endif
				    </ul>
			    </li>
			@endif

			@if(auth()->user()->can('settings-bar'))
				<li class="nav-item">
					<a href="{{route('settings')}}" class="nav-link ">
						<i class="icon-gear"></i>
						<span>
							{{ __('setting') }}
						</span>
					</a>
				</li>
			@endif	
			
		
		</ul>
	</div>
</div>


  <!--All profile view-->

  <script>
    // var mimes = {
    //     'jpeg': 'data:image/jpeg;base64,'
    // };


    //   function encode(data)
    //   {
    //       var str = data.reduce(function(a,b){ return a+String.fromCharCode(b) },'');
    //       return btoa(str).replace(/.{76}(?=.)/g,'$&\n');
    //   }

    //   function getUrlByFileName(fileName,mimeType) {
     
    //       return new Promise(
    //           function (resolve, reject) {
    //               bucket.getObject({Key: fileName}, function (err, file) {
    //                   var result =  mimeType + encode(file.Body);
    //                   resolve(result)
    //               });
    //           }
    //       );
         
    //   }

    //   function openInNewTab(url) {
    //       var redirectWindow = window.open(url, '_blank');
    //       redirectWindow.location;
    //   }
    
    //     if("{{auth()->user()->profile_pic}}" != ""){
    //       getUrlByFileName('{{auth()->user()->profile_pic}}', mimes.jpeg).then(function(data) {
    //     $("#{{auth()->user()->slug}}").attr('src',data);
    //   });
    //   }
    
   


  </script>
		