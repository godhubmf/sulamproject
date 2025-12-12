<div class="content-container">
    <!-- Filter Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 1rem; background: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-subtle);">
        <div>
            <strong style="color: var(--text-primary); margin-right: 0.75rem;">Filter by Role:</strong>
            <select onchange="window.location.href=this.value" class="form-select" style="display: inline-block; width: auto; min-width: 200px;">
                <option value="?" <?php echo $currentRole === null ? 'selected' : ''; ?>>All Users</option>
                <option value="?role=resident" <?php echo $currentRole === 'resident' ? 'selected' : ''; ?>>Residents</option>
                <option value="?role=admin" <?php echo $currentRole === 'admin' ? 'selected' : ''; ?>>Admins</option>
            </select>
        </div>
        <div style="color: var(--muted); font-size: 0.9rem;">
            <?php echo count($users); ?> user<?php echo count($users) !== 1 ? 's' : ''; ?> found
        </div>
    </div>

    <!-- Users Table -->
    <?php if (empty($users)): ?>
        <div class="notice" style="text-align: center; padding: 3rem;">
            <i class="fas fa-users" style="font-size: 3rem; color: var(--muted); margin-bottom: 1rem;"></i>
            <p style="font-size: 1.1rem; color: var(--muted);">No users found.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Income Class</th>
                    <th>Housing Status</th>
                    <th class="table__cell--numeric">Dependents</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th class="table__cell--actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo e($user['name']); ?></td>
                        <td><?php echo e($user['username']); ?></td>
                        <td>
                            <span class="badge <?php echo $user['roles'] === 'admin' ? 'badge-primary' : 'badge-secondary'; ?>">
                                <?php echo e($user['roles']); ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <?php
                                $income = $user['income'];
                                $incomeClass = '-';
                                if ($income !== null && $income !== '') {
                                    if ($income < 5250) {
                                        $incomeClass = 'B40';
                                    } elseif ($income < 11820) {
                                        $incomeClass = 'M40';
                                    } else {
                                        $incomeClass = 'T20';
                                    }
                                }
                                echo $incomeClass;
                            ?>
                        </td>
                        <td style="text-align:center;">
                            <?php
                                $hs = $user['housing_status'] ?? '';
                                if ($hs === 'renting') {
                                    echo 'Renting';
                                } elseif ($hs === 'own_house') {
                                    echo 'Own House';
                                } else {
                                    echo '-';
                                }
                            ?>
                        </td>
                        <td class="table__cell--numeric" style="text-align:center;">
                            <?php echo isset($user['dependent_count']) ? $user['dependent_count'] : 0; ?>
                        </td>
                        <td><?php echo e($user['email']); ?></td>
                        <td><?php echo e($user['phone_number'] ?? '-'); ?></td>
                        <td>
                            <?php echo $user['is_deceased'] ? '<span style="color:red;">Deceased</span>' : 'Active'; ?>
                        </td>
                        <td class="table__cell--actions">
                            <?php if ($user['roles'] === 'resident'): ?>
                                <a href="/admin/waris?user_id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">View Waris</a>
                            <?php endif; ?>
                            <a href="<?php echo url('admin/user-edit?id=' . $user['id']); ?>" class="btn btn-secondary btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>