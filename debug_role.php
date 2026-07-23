<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email','chalodeveloper@gmail.com')->first();
if (!$user) {
    echo "user missing\n";
    exit(0);
}
echo "user exists\n";
echo "password ok: ".(Hash::check('12345678', $user->password) ? 'yes' : 'no')."\n";
echo "roles: ".json_encode($user->getRoleNames()->toArray())."\n";
