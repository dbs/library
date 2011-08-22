<!doctype html>
<?php include('loggedin.php'); ?>
<html>
  <head>
    <title>Add Reserves</title> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"></meta> 
    <style type="text/css"> 
      @import "../js/dojo/resources/dojo.css";
      @import "../js/dijit/themes/tundra/tundra.css"; 
      div.formstuff {
        position: relative;
        width: 300px;
        margin: 0 auto;
      }
      #statuses {
        font-size: 1.5em;
      }
    </style> 
    <!-- Need the following line to initialize dojo-->
    <script type="text/javascript" src="../js/dojo/dojo.js"
      djConfig="isDebug: false, debugAtAllCosts: false, parseOnLoad: true"></script> 
    <script type="text/javascript"> 
      //includes
      dojo.require("dijit.form.Form");
      dojo.require("dijit.form.TextBox");
      dojo.require("dijit.form.Button");

      //function which submits the form data to the server
      function submitStuff() {
        // the form
        var form = dijit.byId("addreserves");
        // connect the submission of the form to a function which submits the data
        dojo.connect(dijit.byId("addreserves"), "onSubmit", function(e) {
          e.preventDefault(); //do not let the form do its default action
          // arguments for a POST request
          var xhrArgs = {
            url: "admin.php?mode=newpost",
            form: 'addreserves',
            load: function(responseObject){
              // update our page to reflect the success
              dojo.byId("statuses").innerHTML = '<font color="#339900">Reserve added</font>';
              form.reset(); // reset the form
            },
            error: function(error){
              // update our page to reflect our failure
              dojo.byId("statuses").innerHTML = '<font color="#FF0000">Error: ' + error + '</font>';
              console.error("Error: " + error); // log the error in the console
            }
          };
          dojo.xhrPost(xhrArgs); //make request
          dojo.byId("statuses").innerHTML = 'Adding...';	//update status on page
        });
      }
      dojo.addOnLoad(submitStuff); // execute this function when dojo has loaded
    </script>
  </head>
  <body class="tundra">
  <div class="formstuff" id="formstuff">
    <img src="../laurentian.jpg" />
    <br />
    <br />

    <h1>Add to Reserves List </h1>
    <br />
    <br />
    <p>Enter the course code, instructor, and the id of the bookbag that you created for this reserve. Press submit, and your entry will be added to the list.</p>
    <!-- Create form. Also create inputs and buttons in the form. Similar to normal HTML form creation -->
    <form dojoType="dijit.form.Form" id="addreserves" jsId="addreserves" encType="multipart/form-data" action="" method="post">
      <table>
      <tr>
        <td><label for="coursecode">Course Code</label></td>
        <td><input type="text" name="coursecode" value="" dojoType="dijit.form.TextBox" trim="true"></td>
      <br />
      </tr>

      <tr>
        <td><label for="instructor">Instructor</label></td>
        <td><input type="text" name="instructor" value="" dojoType="dijit.form.TextBox" trim="false"></td>
      </tr>
      <br />

      <tr>
        <td><label for="bookbagid">Bookbag ID</label></td>
        <td><input type="text" name="bookbagid" value="" dojoType="dijit.form.TextBox" trim="true"></td>
      <br />
      </tr>
    </table>
      <br />
      <div class="formstuff" id="statuses">
      </div>
      <br />
      <button dojoType="dijit.form.Button" type="submit" name="submitButton" value="Submit">
        Submit
      </button>
      <button dojoType="dijit.form.Button" type="reset">
        Clear
      </button>
    </form>

    <h3><a href="logout.php">Logout</a></h3>
  </div>
  </body>
</html>
