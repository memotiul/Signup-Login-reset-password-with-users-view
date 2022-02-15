<?php
 
namespace App\Http\Controllers;
 use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use Hash;
use DB;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
 
class AuthController extends Controller
{
 
    public function index()
    {
        return view('auth.login');
    }
 
    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
 
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('profile')
                        ->withSuccess('Signed in');
        }
 
        return redirect("login")->withSuccess('Login details are not valid');
    }
 
    public function registration()
    {
        return view('auth.registration');
    }
 
    public function customRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
 
        $data = $request->all();
        $check = $this->create($data);
        
        return redirect("dashboard");
    }
 
    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
      session()->flash('success','User Registered Successfully.');
    }
 
    public function dashboard()
    {
        if(Auth::check()){
            return view('dashboard');
        }
    
        return redirect("login")->withSuccess('You are not allowed to access');
    }
 
    public function signOut() {
        Session::flush();
        Auth::logout();
 
        return Redirect('login');
    }
    public function profile()
    {
        return view('profile');
    }

  public function edit()
    {
        return view('changePassword');
    } 
   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
   
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
   
        dd('Password change successfully.');
        return Redirect('profile');
    }
    public function view(){
$users = DB::select('select * from users');
return view('auth.stud_view',['users'=>$users]);
}
}
