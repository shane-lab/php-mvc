// very WIP

$.fn.Menu = function($trigger) {
  var plugin = 'offcanvas';
  
  var $this = $(this);
  function Plugin($this, $trigger) {
    if ($trigger != null && $trigger !== undefined) {
      $trigger.click(function() {
        $this.toggleClass('active');
      });
    }
    
    $item = $this.find('li.item a.active');
    if ($item.length === undefined || $item.length <= 0) {
      $item = null;
      var regex = new RegExp(window.document.URL.toLowerCase() + "[\/]{0,1}$", 'i');
      $.each($this.find('li.item a'), function(i, e) {
        if ($item == null) {
          if (regex.test(e.href)) {
            $item = $(e);
          }
        }
      });
    }

    if ($item != null) {
      var innerhtml = $item.html();
      $item.addClass('active').html('<i class="material-icons">keyboard_arrow_right</i>' + innerhtml);
    }

    // add <i class="material-icons">keyboard_arrow_right</i> to [li.item > a] tag
    
    $(window).on('resize', function() {
      if (window.innerWidth < 1250) {
        if ($this.hasClass('active')) {
          $this.removeClass('active');
        }
      }
    });
  }
  
  if (!$this.data(plugin)) {
    $this.data(plugin, new Plugin($this, $trigger))
  }
  
  return $this.data(plugin);
};