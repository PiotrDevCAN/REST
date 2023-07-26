/**
 *
 */

class displayNavbar {

	page;
	selectors = [];
	selectorsStr;
	userLevel = [];
	userLevelStr;

	constructor() {
		console.log('+++ Function +++ displayNavbar.constructor');

		this.readCurrentPage();
		this.readUserAccess();

		this.setDisplayableUserAccessLevel();
		this.setActiveMenu();
		this.setPlannedOutages();

		this.restrictPageAccess();
		this.restrictNavbarMenuAccess();
		// this.restrictButtonAccess();

		this.listenForDataTableDraw();
		this.listenForDataTableResponsiveDisplay();

		console.log('--- Function --- displayNavbar.constructor');
	}

	readCurrentPage() {
		var page = window.location.pathname.substring(1);
		console.log(page);
		this.page = page;
	}

	readUserAccess() {
		if (isCdi) {
			this.selectors.push('.' + ACCESS_CDI);
			this.userLevel.push('CDI');
		}
		if (isAdmin) {
			this.selectors.push('.' + ACCESS_ADMIN);
			this.userLevel.push('Admin');
		}
		if (isDemand) {
			this.selectors.push('.' + ACCESS_DEMAND);
			this.userLevel.push('Demand');
		}
		if (isSupply) {
			this.selectors.push('.' + ACCESS_SUPPLY);
			this.userLevel.push('Supply');
		}
		if (isSupplyX) {
			this.selectors.push('.' + ACCESS_SUPPLY_X);
			this.userLevel.push('SupplyX');
		}
		if (isRfs) {
			this.selectors.push('.' + ACCESS_RFS);
			this.userLevel.push('Rfs Team');
		}
		if (isRfsADTeam) {
			this.selectors.push('.' + ACCESS_RFS_AD);
			this.userLevel.push('Rfs AD Team');
		}
		if (isReports) {
			this.selectors.push('.' + ACCESS_REPORTS);
			this.userLevel.push('Reports Only');
		}
		if (isUser) {
			this.selectors.push('.' + ACCESS_USER);
			// this.userLevel.push('User');
		}
		this.selectorsStr = this.selectors.join(',');
		if (this.userLevel.length > 4) {
			const half = Math.ceil(this.userLevel.length / 2);    
			this.userLevel = this.userLevel.slice(0, half);
			this.userLevelStr = this.userLevel.join(':')+':others';
		} else {
			this.userLevelStr = this.userLevel.join(':');
		}
	}

	setDisplayableUserAccessLevel() {
		$("#userLevel").html(this.userLevelStr);
	}

	setActiveMenu() {
		$('li[data-pagename="' + this.page + '"]')
			.addClass('active')
			.closest('li.dropdown')
			.addClass('active');
	}

	setPlannedOutages() {
		var badgedContent = plannedOutagesLabel + "&nbsp;" + plannedOutagesBadge;
		$('#' + plannedOutagesId + ' a').html(badgedContent);
	}

	restrictPageAccess() {
		if (this.page != "" && this.page != "index.php" && this.page.substring(0, 3) != 'cdi') {
			var pageAllowed = $('li[data-pagename="' + this.page + '"]').length;
			if (pageAllowed == 0) {
				window.location.replace('index.php');
				alert("You do not have access to:" + this.page);
			}
		}
	}

	restrictNavbarMenuAccess() {
		console.log('will remove restricted buttons from Navbar menu');
		$('.navbarMenuOption').not(this.selectorsStr).remove();
		$('.navbarMenu').not(':has(li)').remove();
	}

	restrictButtonAccess() {
		console.log('will remove restricted buttons from table');
		$('button.' + ACCESS_RESTRICT).not(this.selectorsStr).remove();
	}

	listenForDataTableDraw() {
		var $this = this;
		$(document).on('draw.dt', function () {
			$this.restrictButtonAccess();
		});
	}

	listenForDataTableResponsiveDisplay() {
		var $this = this;
		$(document).on('responsive-display', function () {
			$this.restrictButtonAccess();
		});
	}
}

const DisplayNavbar = new displayNavbar();

export { DisplayNavbar as default };