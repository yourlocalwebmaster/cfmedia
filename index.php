<!DOCTYPE html>
<html lang="en">
<head>
    <title>CafeMedia CSV Parser.</title>
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" type="text/css" rel="stylesheet" media="screen" />
    <link href="bower_components/bootstrap/dist/css/bootstrap-theme.min.css" type="text/css" rel="stylesheet" media="screen" />
    <style>
        button.form-control{
            background: dodgerblue ;
            color:#fff;
        }
    </style>
</head>
<body>
        <div class="container">

            <div class="row">
                <div class="col-md-offset-3 col-md-6 offset">
                    <form  name="uploadform" enctype="multipart/form-data" method="POST" action="lib/process.php">

                        <div class="row"><div class="col-md-12 text-center"><h3>Please select CSV.</h3></div></div>
                        <?php if(isset($_GET['success'])):?>

                                <div class="row"><div class="alert-success alert">
                                    <strong>You may download your goods here:</strong>
                                    <ul>
                                        <li><a href="lib/top_posts.<?=strip_tags($_GET['format']);?>" target="_blank">top_posts.<?=strip_tags($_GET['format']);?></a></li>
                                        <li><a href="lib/other_posts.<?=strip_tags($_GET['format']);?>" target="_blank">other_posts.<?=strip_tags($_GET['format']);?></a></li>
                                        <li><a href="lib/daily_top_posts.<?=strip_tags($_GET['format']);?>" target="_blank">daily_top_post.<?=strip_tags($_GET['format']);?></a></li>
                                    </ul>
                                </div></div>

                        <?php endif;?>

                        <div class="form-group">
                            <label for="csv">Upload CSV.</label>
                            <input class="form-control" type="file" name="csv" required />
                        </div>

                        <div class="form-group">
                            <label for="date">Simulate Daily Top Post</label>
                            <input type="text" name="date" value="Sat Oct 03 02:05:34 2015" required />
                            <div>Should match a true result in the uploaded csv to simulate "TODAY"</div>

                        </div>

                        <div class="form-group">
                            <label for="details">Detailed Mode</label>
                            <select name="details"required>
                                <option value="1" selected>yes</option>
                                <option value="0">no</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="format">Format</label>
                            <select name="format"required>
                                <option value="json">JSON</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="form-control">Parse</button>
                        </div>
                    </form>
                </div>

            </div>
            <div class="row">
                <div class="col-md-offset-3 col-md-6 panel panel-info">
                    <strong>Disclaimers: </strong>
                    <ul>
                        <li>I am not validating input, or sanitizing it etc.</li>
                        <li>I coded this to accept the same string in the timestamp field in the CSV -as the string in the "date" field. This is because I am parsing the time out of it. (hh:mm:ss). I would you used a more scalable approach, but the times were't strtotiming correctly due to the wrong day of week applied to the timestamp.</li>
                        <li>I made the daily post validation feature flaggable / optional. It enabled it will validate against the same requirements as the TOP POSTS.</li>
                </div>
            </div>
        </div>
</body>
</html>
