<?php
   if (isset($_GET['success'])){
       echo '<h1>Database has been cleared.</h1>';
   }else{
       echo '<form name="clear_db" action="' . tep_href_link('clearDB.php', 'action=clear') . '" method="post">
              <h1 style="color:red">WARNING - this will clear all your orders, products, product categories, articles, affiliates, membership billing reports, and rental record history</h1>
              <input type="submit" value="CLEAR DATABASE" onclick="return confirm(\'Are you sure you want to delete this data?\');">
             </form>';
   }
  ?>