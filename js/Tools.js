// Tools for any page - will move many things here in time


// SelectAll/None for any page - 
function ToolSelectAll(e) {
  $(".SelectAllAble").prop("checked",$("#SelectAll").prop("checked"));
}

var SelTools;

function ToolSelect(e,c) { // e= event, c = colnum
  debugger;
// 1 Parse SelTools if not already parsed
// 2 Find each check value
// 3 Go through each thing that is Selectable (see above) set if appropriate, clear otherwise

  if (!SelTools) {
    SelTools = [];
 //   $("#SelTools");// TODO
  }
  

}


function PCatSel(e,pname='PCAT') {
  $('[id^=MPC_').hide();
  var selectedOption = $("input:radio[name=" + pname + "]:checked").val()
  $('#MPC_' + selectedOption).show();
}

$(document).ready(function() {
  //caches a jQuery object containing the header element
  var header = $(".main-header");
  var dhead = header[0]; // jquery to dom
  var scroll = $(window).scrollTop();  
  if (scroll >= 1) header.addClass("fixedheader");
  
  $(window).scroll(function() {
    var scroll = $(window).scrollTop();
    if (scroll >= 1) {
      header.addClass("fixedheader");
    } else {
      header.removeClass("fixedheader");
  	}
  });
  dhead.addEventListener("mouseover",function() {
    var scroll = $(window).scrollTop();
    if (scroll < 1) {
      header.addClass("fixedheader");
    }
  });
  dhead.addEventListener("mouseout",function() {
    var scroll = $(window).scrollTop();
    if (scroll < 1) {
      header.removeClass("fixedheader");
  	}
  });
  $('#HoverContainer').detach().appendTo('#LastDiv');  // Get menu to work on Iphones
});

// Sticky menus for mobiles

var StickyTime;

function RemoveSticky() {
  $('.stick').removeClass('stick');  
}

function NoHoverSticky(e) {
  $('.active').removeClass('active');
}

function HoverSticky(e) {
  NoHoverSticky();
  RemoveSticky();
  e.currentTarget.lastElementChild.className += " active";
}

function NavStick(e) { // Toggle sticking of menus
  if (e.currentTarget.nextElementSibling.classList.contains('stick')) {
    RemoveSticky();
  } else {
    RemoveSticky();
    e.currentTarget.nextElementSibling.className += " stick";
    StickyTime = setTimeout(RemoveSticky,3000);
  }
}

function NavSetPosn(e,labl) {
  // Find Actual width of div labl, position child half of width to the left
  var lablwid = $("#MenuParent" + labl).outerWidth();
  $("#MenuChild" + labl).css({"margin-left":(( lablwid-$('.dropdown-content').width())/2) });
}

var MenuWidths = [470,1000,1290,1380];
var MenuWidthsDonate = [470,1115,1385,1470];

function MenuResize() {
// Work out effective width
// if < Threshold 2 - hide level 2 elements
// Work out effective Width
// if < Threshold 1 then
  // Show Menu Icon
  // copy menus to menu icon
  // hide those that can be hidden
//  return;
//  debugger;
  var Ewidth = $(".Main-Header").outerWidth();
  var IconWidth = $(".header-logo").width();
  if (Ewidth > MenuWidths[3] ) {  // Show all
    $(".MenuIcon").hide();
    CloseHoverMenu();
    $(".MenuMinor0").show();
    $(".MenuMinor1").show();
    $(".MenuMinor2").show();
    $("#MenuBars").css({"right":0,"width":(Ewidth-IconWidth-40)});
    $(".WMFFBannerText").css({"font-size":'44pt'});
  } else {
    $(".MenuMinor2").hide();
    if (Ewidth < MenuWidths[2]) { // Show limited
      $(".MenuMinor1").hide();
      $("#MenuBars").css({"right":80, "width":(Ewidth-IconWidth-120)});
      $(".WMFFBannerText").css({"font-size":'40pt'});
      if (Ewidth < MenuWidths[1]) { // Show none
        $(".MenuMinor0").hide();
        if (Ewidth < MenuWidths[0]) { // Not even the dates!!
          $(".FestDates").hide();
          $(".SmallDates").show();
          $(".WMFFBannerText").css({"font-size":'20pt'});
        } else {
          $(".FestDates").show();
          $(".SmallDates").hide();
          $(".WMFFBannerText").css({"font-size":'24pt'});
        }
      } else {
        $(".MenuMinor0").show();
        $(".FestDates").show();
        $(".SmallDates").hide();
        $(".WMFFBannerText").css({"font-size":'34pt'});
      }
      $(".MenuIcon").show();
    } else { // Show most
      $(".MenuIcon").hide();    
      CloseHoverMenu();
      $(".MenuMinor0").show();
      $(".MenuMinor1").show();
      $("#MenuBars").css({"right":0, "width":(Ewidth-IconWidth-40)});
      $(".WMFFBannerText").css({"font-size":'40pt'});
    }
  }
}


$(document).ready(function() {
  if ($('#MenuDonate')) MenuWidths = MenuWidthsDonate;
  MenuResize();
  window.addEventListener('resize',MenuResize);  
})


function ShowHoverMenu() {
//  debugger;
//  if (!MenuCopied) CopyHoverMenu();

  $("#HoverContainer").show();
  $("#HoverContainer").addClass("Slide-Left");
  $(".MenuMenuIcon").hide();
  $(".MenuMenuClose").show();
  $(".MenuSubMenu").hide();
  $(".MenuSubMenuIcon").hide();
}

function CloseHoverMenu() {
  $("#HoverContainer").hide();
  $("#HoverContainer").removeClass("Slide-Left");
  $(".MenuMenuIcon").show();
  $(".MenuMenuClose").hide();
}

function HoverDownShow(labl) {
  $("#HoverChild" + labl).toggle();
  $("#DownArrow" + labl).toggleClass("Flip");
}


var isAdvancedUpload = function() {
  var div = document.createElement('div');
  return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
}();

function InvoiceCatChange(e,v) {
//  debugger;
  $('.InvOrg1').hide();
  $('.InvOrg2').hide();
  if (v == 0) $('.InvOrg1').show();
  if (v == 1) $('.InvOrg2').show();
}

// Maintain id D for size siz form fld
function SetDSize(D,siz,fld) {
  var len = document.getElementById(fld).value.length;
  document.getElementById(D).innerHTML = "<br><b>(" + len + "/" + siz + ")</b>";
}

function getDocHeight(doc) {
    doc = doc || document;
    // stackoverflow.com/questions/1145850/
    var body = doc.body, html = doc.documentElement;
    var height = Math.max( body.scrollHeight, body.offsetHeight, 
        html.clientHeight, html.scrollHeight, html.offsetHeight );
    return height;
}

function setIframeHeight(id) {
    var ifrm = document.getElementById(id);
    var doc = ifrm.contentDocument? ifrm.contentDocument: 
        ifrm.contentWindow.document;
    ifrm.style.visibility = 'hidden';
    ifrm.style.height = "10px"; // reset to minimal height ...
    // IE opt. for bing/msn needs a bit added or scrollbar appears
    ifrm.style.height = getDocHeight( doc ) + 4 + "px";
    ifrm.style.visibility = 'visible';
}

function AddLineUpHighlight(id) {
//  debugger;
  $('#LineUp' + id).addClass("LUHighlight");
//  var xx=1;
}

function RemoveLineUpHighlight(id) {
  $('#LineUp' + id).removeClass("LUHighlight");
}

// Onload functions
function Set_MinHeight(p1,p2) {
  $(p2).css({"min-height":$(p1).height()});
}

function Set_ColBlobs(Blobs,MaxBlob) {
  if ($(".Main-Header").outerWidth() <= 800) {
    $(".OneCol").removeClass("OneCol"); // Wont work on resize (yet)
  } else {
    for (var B = 1; B <= MaxBlob; B++) {
      var ht1 = $('#TwoCols1').height(); 
      var ht2 = $('#TwoCols2').height(); 
      var Bht = $('#' + Blobs + B).height();
      if ((ht1 - Bht) > ht2) $('#' + Blobs + B).detach().appendTo('#TwoCols2');
    }
  }
}

var AfterInputs = [];

function Register_AfterInput(fun,p1,p2) {
  AfterInputs.push([fun,p1,p2]);
}

function DoAfterInputs() {
//  debugger;
  if (!AfterInputs) return;
  for (var f in AfterInputs) {
    [fun,p1,p2] = AfterInputs[f];
    fun(p1,p2);
  }
}


function AutoInput(f,after) {
//  debugger;
  var newval = document.getElementById(f).value;
  var id = f;
  if (document.getElementById(f).newid ) id = document.getElementById(f).newid;
  var yearval = (document.getElementById('Year') ? (document.getElementById('Year').value || 0) : 0);
  var typeval = document.getElementById('AutoType').value;
  var refval = document.getElementById('AutoRef').value;
  var Indexed = document.getElementById('AutoIndexed') ? document.getElementById('AutoIndexed').value : 0;
  if (Indexed) {
    var m = f.match(/(.+?)(\d+)/);
    var Paras = {'D':typeval, 'F':m[1], 'V':newval, 'Y':yearval, 'I':m[2]};      
  } else {
    var Paras = {'D':typeval, 'F':id, 'V':newval, 'Y':yearval, 'I':refval};  
  }

  $.post("formfill.php", Paras , function( data ) {
    var elem = document.getElementById(f);
    var m = data.match(/^\s*?@(.*)@/);
    if (m) {
      elem.newid = elem.name = m[1];
    } else if (m = data.match(/^\s*?#(.*)#/)) { // Photo update 
      m = data.split('#')
      elem.value = m[1];
      document.getElementById(m[2]).src = m[3];
    } else if (m = data.match(/^\s*!(.*)!/)) $('#ErrorMessage').html( m[1] );

    var dbg = document.getElementById('Debug');
    if (dbg) $('#Debug').html( data) ;  
    DoAfterInputs();
    if (data.match(/FORCERELOAD54321/m)) {
      setTimeout(function(){
//        var Location = window.location.pathname + "?id=" + refval;  //  window.location.hostname
//        window.location.href = Location;

        window.location.reload();
        }, 100);
    } else if (data.match(/FORCELOADCHANGE54321/m)) {
      setTimeout(function(){
        var Location = window.location.pathname + "?id=" + refval;  //  window.location.hostname
        window.location.href = Location;
        }, 100);
    } else if (data.match(/CALLxxAFTER/m)) {
      after(f);
    } else if (m=data.match(/REPLACE_ID_WITH:(.*) /m)) {
     if (document.getElementById(f)) document.getElementById(f).id = m[1];    
    }
  });
}

function AutoCheckBoxInput(f) {
//  debugger;
  var cbval = document.getElementById(f).checked;
  var id = f;
//  if (document.getElementById(f).newid ) id = document.getElementById(f).newid;
  var newval = (cbval?1:0); 
  var yearval = (document.getElementById('Year') ? (document.getElementById('Year').value || 0) : 0);
  var typeval = document.getElementById('AutoType').value;
  var refval = document.getElementById('AutoRef').value;
  var Indexed = document.getElementById('AutoIndexed') ? document.getElementById('AutoIndexed').value : 0;
  if (Indexed) {
    var m = f.match(/(.+?)(\d+)/);
    var Paras = {'D':typeval, 'F':m[1], 'V':newval, 'Y':yearval, 'I':m[2]};      
  } else {
    var Paras = {'D':typeval, 'F':id, 'V':newval, 'Y':yearval, 'I':refval};  
  }

  var dbg = document.getElementById('Debug');
  if (dbg) {
    $.post("formfill.php", Paras , function( data ) { $('#Debug').html( data)});
  } else {
    $.post("formfill.php", Paras );
  }
  DoAfterInputs();
}

function AutoRadioInput(f,i) {
//  debugger;
  var id = f;
//  if (document.getElementById(f).newid ) id = document.getElementById(f).newid;

  var newval = document.getElementById(f+i).value;
  var yearval = (document.getElementById('Year') ? (document.getElementById('Year').value || 0) : 0);
  var typeval = document.getElementById('AutoType').value;
  var refval = document.getElementById('AutoRef').value;
  var Indexed = document.getElementById('AutoIndexed') ? document.getElementById('AutoIndexed').value : 0;
  if (Indexed) {
    var m = f.match(/(.+?)(\d+)/);
    var Paras = {'D':typeval, 'F':m[1], 'V':newval, 'Y':yearval, 'I':m[2]};      
  } else {
    var Paras = {'D':typeval, 'F':id, 'V':newval, 'Y':yearval, 'I':refval};  
  }

  var dbg = document.getElementById('Debug');
  DoAfterInputs();
  if (dbg) {
    $.post("formfill.php", Paras , function( data ) { $('#Debug').html( data);
    if (data.match(/FORCERELOAD54321/m)) {
      setTimeout(function(){
        window.location.reload();
        }, 100);
    } else if (data.match(/FORCELOADCHANGE54321/m)) {
      setTimeout(function(){
        var Location = window.location.pathname + "?id=" + refval;  //  window.location.hostname
        window.location.href = Location;
        }, 100);
    } 
   });
  } else {
    $.post("formfill.php", Paras);
  }

}

var LoadStack = [];

function Register_Onload(fun,p1,p2) {
  LoadStack.push([fun,p1,p2]);
}

function Refresh_Image_After_Upload(T,V) {
  if (s=document.getElementById(T)) s.value = V;
} 

$(document).ready(function() {
//  debugger;
  if (!LoadStack) return;
  for (var f in LoadStack) {
    [fun,p1,p2] = LoadStack[f];
    fun(p1,p2);
  }
})


