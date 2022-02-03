(function() {
  /*
  // Variables
  */
  var $container = createNotifyContainer();

  /*
  // Methods
  */
  function init() {
    // Add container to hold notify item
    document.body.append($container);

    window.notify = notify;
  }

  function notify(options) {
    if (!isOptionsValid(options)) return;

    var $item = createNotifyItem(
      options.message || "",
      options.color || "default"
    );

    if (options.timeout) {
      setAutocloseTimeout($item, options.timeout);
    }

    setCloseOnClick($item);

    $container.append($item);
  }

  function createNotifyContainer() {
    var $container = document.createElement("div");
    $container.className = "notify-container";

    return $container;
  }

  function createNotifyItem(message, color) {
    var $item = document.createElement("div");
    $item.classList.add("notify-item");
    $item.classList.add("notify-item--" + color);
    $item.innerHTML = message;

    return $item;
  }

  function setCloseOnClick($el) {
    $el.addEventListener("click", function() {
      $el.remove();
    });
  }

  function setAutocloseTimeout($el, timeout) {
    setTimeout(function() {
      $el.remove();
    }, timeout);
  }

  function isOptionsValid(options) {
    return (
      options ||
      console.error('usage: \n notify({ message: "OK", color: "success", timeout: 3000 })')
    );
  }

  /*
  // Init
  */

  init();
})();
