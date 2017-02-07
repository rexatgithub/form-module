<?php namespace Modules\Form\Http\Controllers;

use App\Services\GoogleSpreadsheetService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Blog\Repositories\PostRepository;
use Modules\Core\Http\Controllers\BasePublicController;

class PublicController extends BasePublicController
{

    public $avenewfunding;
    private $mailer;

    public function __construct()
    {
        parent::__construct();
        $this->avenewfunding = $this->avenewfunding == null ? (new GoogleSpreadsheetService("AveNewFunding")) : $this->avenewfunding;
        if ($this->mailer == null) {
            $this->mailer = new \PHPMailer();
            $this->mailer->IsSMTP(); // telling the class to use SMTP
            $this->mailer->SMTPDebug = 1; // enables SMTP debug information (for testing)
            // 1 = errors and messages
            // 2 = messages only
            $this->mailer->SMTPAuth = true; // enable SMTP authentications
            $this->mailer->SMTPSecure = "ssl"; // sets the prefix to the servier
            $this->mailer->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server
            $this->mailer->Port = 465; // set the SMTP port for the GMAIL server
            //$mail->IsHTML(true);
            $this->mailer->Username = "sunderwood@channelgrowth.com"; // GMAIL username
            $this->mailer->Password = "v5tmoWCYSPrX"; // GMAIL password
            $this->mailer->SetFrom("contact-us@avenewfunding.com", "AveNewFunding - Contact Us");
        }
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

    public function contactUs(Request $request)
    {
        $body = file_get_contents(base_path("public/email/contactus.html"));
        $from = str_replace("@sender-tmp", '<' . $request['name'] . '>' . $request['email'], $body);
        $content = str_replace("@content-tmp", $request['message'], $from);

        $result = $this->_sendEmail("AveNewFunding - " . $request['subject'], 'rcambarijan@channelgrowth.com ', null, $content, [], 'rcambarijan@channelgrowth.com');
        return json_encode($result);
    }

    private function _sendEmail($subject, $mainrecipient, $cc, $body, $attachments = [], $bcc = null)
    {
        $response = array();
        /* Initialize PHPMailer */

        $this->mailer->Subject = $subject;
        $this->mailer->AddAddress($mainrecipient);
        $this->mailer->Body = $body;
        $this->mailer->isHTML(true);

        if ($cc != null)
            $this->mailer->AddCC($cc);

        if ($bcc != null)
            $this->mailer->addBCC($bcc);

        if (!empty($attachments)) {
            $attachments = is_array($attachments) ? $attachments : json_decode($attachments, true);
            foreach ($attachments as $key => $attachment) {
                $this->mailer->addAttachment(base_path("public/" . $attachment), $key);
            }
        }

        if (!$this->mailer->Send()) {
            $response["error"] = 1;
            $response["message"] = "Mailer Error: " . $this->mailer->ErrorInfo . "\n";
        } else {
            $response["error"] = 0;
            $response["message"] = "Done sending email...\n";
        }
        return $response;
    }

    public function recents(PostRepository $post){
        $latestPosts = $post->latest(3);
        return compact('latestPosts');
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
