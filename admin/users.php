<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-5">
                        <h4>User Lists</h4>
                    </div>
                    <div class="col-md-7">
                        <!-- Add Focal Person Button -->
                        <a href="generate-focalperson.php" class="btn btn-primary float-end">Add Focal Person</a>

                        <form action="" method="GET">
                            <div class="row">
                                <!-- Role Filter -->
                                <div class="col-md-8">
                                    <select name="role" required class="form-select">
                                        <option value="">Select Role</option>
                                        <option value="user" <?= (isset($_GET['role']) && $_GET['role'] == 'user') ? 'selected' : '' ?>>User</option>
                                        <option value="focal_person" <?= (isset($_GET['role']) && $_GET['role'] == 'focal_person') ? 'selected' : '' ?>>Focal Person</option>
                                        <option value="admin" <?= (isset($_GET['role']) && $_GET['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </div>

                                <!-- Buttons -->
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="users.php" class="btn btn-danger">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

                <table id="myTable" class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>User Id</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                        // Fetch user data using PDO with optional role filter
                        $roleRaw = isset($_GET['role']) ? trim($_GET['role']) : null;
                        $roleFilter = null;
                        if ($roleRaw) {
                            // map UI role values to DB role values
                            if ($roleRaw === 'focal_person') {
                                $roleFilter = 'fperson';
                            } elseif (in_array($roleRaw, ['admin','researcher','user','fperson'])) {
                                $roleFilter = $roleRaw;
                            }
                        }

                        if ($roleFilter) {
                            $query = "SELECT * FROM users WHERE role = :role";
                            $stmt = $pdo->prepare($query);
                            $stmt->execute([':role' => $roleFilter]);
                        } else {
                            $query = "SELECT * FROM users";
                            $stmt = $pdo->prepare($query);
                            $stmt->execute();
                        }
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($users && count($users) > 0) {
                            foreach ($users as $userItem) {
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($userItem['id']); ?></td>
                                        <td><?= htmlspecialchars($userItem['fname']); ?></td>
                                        <td><?= htmlspecialchars($userItem['lname']); ?></td>
                                        <td><?= htmlspecialchars($userItem['email']); ?></td>
                                        <td><?= htmlspecialchars($userItem['role']); ?></td>
                                        <td><?= $userItem['is_ban'] == 1 ? 'Banned' : 'Active'; ?></td>
                                        <td>
                                            <a href="user-edit.php?id=<?= $userItem['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                                            <a href="user-delete.php?id=<?= $userItem['id']; ?>" class="btn btn-danger btn-sm mx-2" onclick="return confirm('Are you sure you want to delete this data?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="7">No Record Found</td>
                            </tr>
                            <?php
                        }
                        ?>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>


<?php include('includes/footer.php'); ?>