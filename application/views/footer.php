  </div>
    <footer style="width: 100%; text-align: center; color: grey">
      <strong>Copyright &copy; 2024 <a href="#">Classroom Attendance Portal</a>.</strong> All rights reserved.
    </footer>
  </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="<?php base_url('assets/dist/js/adminlte.js'); ?>"></script>
    <script>
      $(document).ready(function() {
        $('.toggle-sidebar').click(function() {
          $('.main-sidebar').toggleClass('d-none');
          $('.content-wrapper').toggleClass('ml-0');
          $('.main-header').toggleClass('ml-0');
        });
      });
    </script>
</body>
</html>