<?php 
session_start();
include('includes/config.php');

if (isset($_GET['nid'])) {
    $nid = intval($_GET['nid']);
    $query = mysqli_query($con, "SELECT tblposts.PostTitle as posttitle, tblposts.PostImage, tblcategory.CategoryName as category, tblsubcategory.Subcategory as subcategory, tblposts.PostDetails as postdetails, tblposts.PostingDate as postingdate FROM tblposts LEFT JOIN tblcategory ON tblcategory.id=tblposts.CategoryId LEFT JOIN tblsubcategory ON tblsubcategory.SubCategoryId=tblposts.SubCategoryId WHERE tblposts.id='$nid' AND tblposts.Is_Active=1");
    $row = mysqli_fetch_array($query);
    if (!$row) {
        echo "Recipe not found!";
        exit;
    }
} else {
    echo "Invalid recipe ID!";
    exit;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitComment'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $comment = mysqli_real_escape_string($con, $_POST['comment']);
    $postId = $nid;

    if (!empty($name) && !empty($email) && !empty($comment)) {
        $insertQuery = "INSERT INTO tblcomments (postId, name, email, comment, status) VALUES ('$postId', '$name', '$email', '$comment', 1)";
        mysqli_query($con, $insertQuery);
        echo "<script>alert('Comment added successfully!');</script>";
    } else {
        echo "<script>alert('Please fill in all fields!');</script>";
    }
}

// Fetch approved comments
$commentsQuery = mysqli_query($con, "SELECT name, email, comment, postingDate FROM tblcomments WHERE postId='$nid' AND status=1 ORDER BY postingDate DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlentities($row['posttitle']); ?> | Recipe Details</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/modern-business.css" rel="stylesheet">
    <style>
        .badge-custom {
            margin-right: 5px;
            font-size: 0.9em;
        }
        .comment-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .comment-form {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container mt-4">
        <!-- Recipe Details -->
        <div class="row">
            <div class="col-md-12">
                <h1 class="mt-4"><?php echo htmlentities($row['posttitle']); ?></h1>
                <p class="lead">
                    <span class="badge badge-success badge-custom"><?php echo htmlentities($row['category']); ?></span>
                    <span class="badge badge-warning badge-custom"><?php echo htmlentities($row['subcategory']); ?></span>
                </p>
                <p>Posted on <?php echo htmlentities($row['postingdate']); ?></p>
                <img class="img-fluid rounded" src="admin/postimages/<?php echo htmlentities($row['PostImage']); ?>" alt="<?php echo htmlentities($row['posttitle']); ?>">
                <hr>
                <p><?php echo htmlentities($row['postdetails']); ?></p>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="row">
            <div class="col-md-12">
                <h4 class="mt-5">Comments</h4>

                <!-- Display Comments -->
                <?php while ($commentRow = mysqli_fetch_array($commentsQuery)) { ?>
                    <div class="comment-box">
                        <strong><?php echo htmlentities($commentRow['name']); ?></strong>
                        <span class="text-muted" style="font-size: 0.9em;"> - <?php echo htmlentities($commentRow['postingDate']); ?></span>
                        <p><?php echo htmlentities($commentRow['comment']); ?></p>
                    </div>
                <?php } ?>

                <!-- Comment Form -->
                <div class="comment-form">
                    <h5>Leave a Comment</h5>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="comment">Comment</label>
                            <textarea class="form-control" name="comment" id="comment" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="submitComment" class="btn btn-primary mt-2">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
