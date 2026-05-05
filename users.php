<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $profile = $_POST['profile'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, profile) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $password, $role, $profile]);
        $success = "Utilizator adăugat cu succes!";
    } catch (PDOException $e) {
        $error = "Eroare: Numele de utilizator există deja.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = $_POST['user_id'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $profile = $_POST['profile'];

    try {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, role = ?, profile = ? WHERE id = ?");
            $stmt->execute([$username, $password, $role, $profile, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, profile = ? WHERE id = ?");
            $stmt->execute([$username, $role, $profile, $id]);
        }
        $success = "Utilizator actualizat cu succes!";
    } catch (PDOException $e) {
        $error = "Eroare la actualizarea utilizatorului.";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: users.php");
        exit();
    } else {
        $error = "Nu te poți șterge pe tine însuți.";
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY role ASC, username ASC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-bold">Gestionare Utilizatori</h2>
        <p class="text-gray-400">Administrare conturi Staff și Admin</p>
    </div>
    <div class="flex gap-3">
        <a href="export_users.php" class="bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white border border-emerald-600/30 px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2">
            <i class="fas fa-file-export"></i> Export
        </a>
        <a href="import_users.php" class="bg-amber-600/20 hover:bg-amber-600 text-amber-400 hover:text-white border border-amber-600/30 px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2">
            <i class="fas fa-file-import"></i> Import
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
        <div class="glass p-6 rounded-2xl">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                <i class="fas fa-user-plus text-blue-500"></i> Adaugă Utilizator
            </h3>

            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-3 rounded-xl mb-4 text-sm">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-500/10 border border-green-500/50 text-green-400 p-3 rounded-xl mb-4 text-sm">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4" id="userForm">
                <input type="hidden" name="add_user" value="1" id="formAction">
                <input type="hidden" name="user_id" id="editUserId">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Username</label>
                    <input type="text" name="username" id="formUsername" required class="w-full bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1" id="passLabel">Parolă</label>
                    <input type="password" name="password" id="formPassword" required class="w-full bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500">
                    <p class="text-[10px] text-gray-500 mt-1 hidden" id="passHint">Lasă gol pentru a păstra parola actuală</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Rol</label>
                    <select name="role" id="formRole" class="w-full bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 appearance-none">
                        <option value="staff" class="bg-slate-900">Staff</option>
                        <option value="admin" class="bg-slate-900">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Profil Management</label>
                    <select name="profile" id="formProfile" class="w-full bg-white/5 border border-white/10 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 appearance-none">
                        <option value="" class="bg-slate-900">- Selectează Profil -</option>
                        <option value="Tehnician IT" class="bg-slate-900">Tehnician IT</option>
                        <option value="Tehnician" class="bg-slate-900">Tehnician</option>
                        <option value="Receptioner" class="bg-slate-900">Receptioner</option>
                        <option value="Guvernanta" class="bg-slate-900">Guvernanta</option>
                        <option value="Sef Departament Receptie" class="bg-slate-900">Sef Departament Receptie</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" id="submitBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-xl transition">
                        Creează Cont
                    </button>
                    <button type="button" id="cancelEdit" class="hidden bg-slate-700 hover:bg-slate-600 text-white font-bold py-2 px-4 rounded-xl transition">
                        Anulează
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4">Utilizator</th>
                        <th class="px-6 py-4">Profil</th>
                        <th class="px-6 py-4">Rol</th>
                        <th class="px-6 py-4">Creat la</th>
                        <th class="px-6 py-4 text-right">Acțiuni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center font-bold text-xs">
                                    <?php echo strtoupper(substr($u['username'], 0, 1)); ?>
                                </div>
                                <span class="font-medium"><?php echo htmlspecialchars($u['username']); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-blue-400 font-semibold">
                                    <?php echo $u['profile'] ?: '-'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase <?php echo $u['role'] == 'admin' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-slate-500/20 text-slate-400 border border-slate-500/30'; ?>">
                                    <?php echo $u['role']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                <?php echo date('d.m.Y', strtotime($u['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <button onclick='editUser(<?php echo json_encode($u); ?>)' class="text-blue-500 hover:text-blue-400 transition">
                                    <i class="fas fa-user-edit"></i>
                                </button>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="?delete=<?php echo $u['id']; ?>" onclick="return confirm('Sigur dorești să ștergi acest utilizator?')" class="text-red-500 hover:text-red-400 transition">
                                        <i class="fas fa-user-minus"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('formAction').name = 'edit_user';
    document.getElementById('editUserId').value = user.id;
    document.getElementById('formUsername').value = user.username;
    document.getElementById('formRole').value = user.role;
    document.getElementById('formProfile').value = user.profile || '';

    document.getElementById('formPassword').required = false;
    document.getElementById('passLabel').innerText = 'Parolă Nouă (opțional)';
    document.getElementById('passHint').classList.remove('hidden');
    document.getElementById('submitBtn').innerText = 'Salvează Modificările';
    document.getElementById('cancelEdit').classList.remove('hidden');

    document.getElementById('userForm').scrollIntoView({ behavior: 'smooth' });
}

document.getElementById('cancelEdit').onclick = function() {
    document.getElementById('formAction').name = 'add_user';
    document.getElementById('editUserId').value = '';
    document.getElementById('formUsername').value = '';
    document.getElementById('formRole').value = 'staff';
    document.getElementById('formProfile').value = '';

    document.getElementById('formPassword').required = true;
    document.getElementById('passLabel').innerText = 'Parolă';
    document.getElementById('passHint').classList.add('hidden');
    document.getElementById('submitBtn').innerText = 'Creează Cont';
    this.classList.add('hidden');
};
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
