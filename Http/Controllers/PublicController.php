<?php namespace Modules\Form\Http\Controllers;

use App\Services\GoogleSpreadsheetService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Core\Http\Controllers\BasePublicController;

class PublicController extends BasePublicController
{

    public $avenewfunding;

    public function __construct()
    {
        parent::__construct();
        $this->avenewfunding = $this->avenewfunding == null ? (new GoogleSpreadsheetService("AveNewFunding")) : $this->avenewfunding;
    }

    public function index()
    {
        return view('form.index');
    }

    public function businessInformation()
    {
        $session = $this->session_get();
        if ($session === false) {
            return redirect('/');
        }
        return view('form.businessinfo', compact('session'));
    }

    public function businessReference()
    {
        $session = $this->session_get();
        if ($session === false) {
            return redirect('/');
        }
        return view('form.businessref', compact('session'));
    }

    public function loanDetails()
    {
        $session = $this->session_get();
        if ($session === false) {
            return redirect('/');
        }
        return view('form.loandetails', compact('session'));
    }

    public function businessOwner()
    {
        $session = $this->session_get();
        if ($session === false) {
            return redirect('/');
        }
        return view('form.businessowner', compact('session'));
    }

    public function authorization()
    {
        $session = $this->session_get();
        if ($session === false) {
            return redirect('/');
        }
        return view('form.authorization', compact('session'));
    }

    public function documents()
    {
        $session = $this->session_get();
        if ($session === false) {
            return redirect('/');
        }
        return view('form.documents', compact('session'));
    }

    public function finish()
    {
        Session::flush();
        return view('form.finish', compact(''));
    }

    public function personal()
    {
        $session = $this->session_get();
        if ($session === false) {
            return redirect('/');
        }
        return view('form.personal', compact('session'));
    }

    public function logout()
    {
        Session::flush();
        return redirect('/');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $now = Carbon::now();
        $data = $request->all();
        $created_at = $now->toDateTimeString();

        if (isset($request['uuid'])) {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $uuid = uniqid() . $now->format("FdYhs");
                $fileName = $uuid . '.' . $file->getClientOriginalExtension();
                $target_dir = $_SERVER["DOCUMENT_ROOT"] . "/uploads/";
                $file->move($target_dir, $fileName);
                return json_encode(array('file' => $fileName));
            }
            if (isset($data['password'])) {
                $data['password'] = sha1($data['password']);
            }
            $this->update_data($data, $request['uuid']);
            return json_encode(['error' => 0, 'data' => 'Success']);
        } else {

            $uuid = uniqid() . $now->format("FdY");
            $data["userid"] = $uuid;
            $data["applydate"] = $created_at;
            $this->avenewfunding->insert($data);
            $this->sesion_add($data);

            return json_encode([
                'error' => 0
                , 'data' => [
                    'id' => $uuid
                    , 'inception' => $data['businessinceptionmonth']
                    , 'amount' => $data['amount']
                    , 'annualrevenue' => $data['annualrevenue']
                ]
            ]);
        }
    }

    private function update_data($data, $uuid)
    {
        $this->sesion_add($data);
        $this->avenewfunding->update($data, "Sheet1", "userid=\"" . $uuid . "\"");
    }

    /**
     * @param $newval
     * @param string $key
     */
    private function sesion_add($newval)
    {
        if (Session::get('SESSION') != null) {
            $old = Session::get('SESSION');
            Session::put('SESSION', array_merge($old, $newval));
        } else {
            Session::put('SESSION', $newval);
        }
        Session::save();
    }

    private function session_get()
    {
        if (Session::get('SESSION') != null) {
            return Session::get('SESSION');
        }
        return false;
    }
}
