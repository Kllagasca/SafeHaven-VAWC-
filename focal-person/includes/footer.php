<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0"/>

<footer class="footer pt-3  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                © <script>
                  document.write(new Date().getFullYear())
                </script>,
                made with <i class="fa fa-heart"></i> by
                <a href="../../index.php" class="font-weight-bold" target="_blank">Honey Bunch</a>
                for a better web.
              </div>
            </div>
            <div class="col-lg-6">
              <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                <li class="nav-item">
                  <a href="./../index.php" class="nav-link text-muted" target="_blank">Honey Bunch</a>
                </li>
                <li class="nav-item">
                  <a href="./../about-us.php" class="nav-link text-muted" target="_blank">About Us</a>
                </li>
                <li class="nav-item">
                  <a href="./../contact.php" class="nav-link text-muted" target="_blank">Contact Us</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>

      </main>
  <?php /* Inline AI chat popup inside the fixed-plugin (replaces configurator) */ ?>
  <div id="chat-fixed-plugin" class="fixed-plugin">
    <button id="chat-plugin-toggle" class="fixed-plugin-button text-white position-fixed px-3 py-2" style="background:#0d6efd;border:none;border-radius:50%;right:20px;bottom:20px;z-index:2200;width:56px;height:56px;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 18px rgba(0,0,0,0.12);">
      <i class="fa fa-comment" style="font-size:20px;color:#fff"></i>
    </button>

    <?php $ts = @filemtime(__DIR__ . '/../../ai_chatbot/styles.css') ?: time(); ?>
    <link rel="stylesheet" href="/SafeHaven/ai_chatbot/styles.css?v=<?php echo $ts; ?>">

    <!-- Inline chat popup (from ai_chat.php) -->
            <div class="chat-popup" id="chat-popup" style="display:none;position:fixed;right:20px;bottom:90px;z-index:2201;">
        <div class="chat-header">
            <div class="header-info">
          <img class="chatbot-logo" src="../assets/img/logo.png" width="50" height="50" alt="SafeHaven logo" style="background:#fff;border-radius:50%;padding:6px;flex-shrink:0;">
                <h2 class="logo-text">SafeHaven AI</h2>
            </div>
                <button id="close-chatbot" class="material-symbols-rounded">keyboard_arrow_down</button>
        </div>


        <div class="chat-body"></div>

        <div class="chat-footer">
            <form action="#" class="chat-form">
                <textarea placeholder="Message..." class="message-input" required></textarea>
                <div class="chat-controls">
                        <div class="file-upload-wrapper">
                            <input type="file" accept="images/*" id="file-input" hidden>
                            <img src="#">
                            <button type="button" id="file-upload" class="material-symbols-rounded">attach_file</button>
                             <button type="button" id="file-cancel" class="material-symbols-rounded">close</button>
                        </div>
                    <button type="submit" id="send-message" class="material-symbols-rounded">arrow_upward</button>
                </div>
            </form>
        </div>
    </div>

  <!-- hidden toggler for script.js so it can bind to #chatbot-toggler when loaded -->
  <button id="chatbot-toggler" style="display:none"></button>
  <?php $tsJs = @filemtime(__DIR__ . '/../../ai_chatbot/script.js') ?: time(); ?>
  <script src="/SafeHaven/ai_chatbot/script.js?v=<?php echo $tsJs; ?>"></script>

    <style>
      /* ensure chat popup sits above other UI */
      .chat-popup{ width:360px; height:520px; background:#fff; border-radius:8px; box-shadow:0 12px 40px rgba(0,0,0,0.12); overflow:hidden; }
      .fixed-plugin-button{ cursor:pointer; }
    </style>

    <script>
      (function(){
        const pluginToggle = document.getElementById('chat-plugin-toggle');
        const chatPopup = document.getElementById('chat-popup');
        const chatbotToggler = document.getElementById('chatbot-toggler');

        // if chatbotToggler not present, create a hidden one for script.js compatibility
        if (!chatbotToggler){
          const hidden = document.createElement('button');
          hidden.id = 'chatbot-toggler';
          hidden.style.display = 'none';
          document.body.appendChild(hidden);
        }

        pluginToggle.addEventListener('click', function(e){
          e.preventDefault();
          const isOpen = chatPopup.style.display === 'block';
          chatPopup.style.display = isOpen ? 'none' : 'block';
          // toggle body class so script.js behaviors (if any) still work
          document.body.classList.toggle('show-chatbot', !isOpen);
        });
      })();
    </script>

  <!--   Core JS Files   -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="assets/js/plugins/chartjs.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

  <script>
    $(document).ready(function() {
        $(".mySummernote").summernote({
          height: 250
        });
        $('.dropdown-toggle').dropdown();
    });
</script>

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready( function () {
    $('#myTable').DataTable({
      "language": {
        "paginate": {
          "previous": "<", // Change "Previous" to "<"
          "next": ">" // Change "Next" to ">"
        }
      }
    });
  });
</script>

  
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/soft-ui-dashboard.min.js?v=1.1.0"></script>
</body>

</html>