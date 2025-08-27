<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Library\ChimeToolProvider;
use Auth;
use Illuminate\Support\Facades\Cookie;
use \App\Chime;
use Packback\Lti1p3\LtiOidcLogin;
use Packback\Lti1p3\LtiMessageLaunch;
use Packback\Lti1p3\LtiException;
use \App\Models\LTI13ResourceLink;
use \App\Library\LTI13Processor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;

class LTI13Handler extends Controller
{

    private $staffRoles = [
    "http://purl.imsglobal.org/vocab/lis/v2/membership#ContentDeveloper",
    "http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor"];

    public function __construct() {
        if(app()->getProvider('debugbar')) {
            app('debugbar')->disable();
        }
    }

    public function index() {

    }

     public function login() {
        return LtiOidcLogin::new(new \App\Library\LTI13Database, new \App\Library\LTI13Cache, new \App\Library\LTI13Cookie)
            ->doOidcLoginRedirect(url("lti13/launch"))
            ->doRedirect();
    }

    public function launch() {

        if(isset($_REQUEST['error']) && $_REQUEST['error'] == 'launch_no_longer_valid') {
            $exception = new \Exception($_REQUEST['error_description']);
            if (app()->bound('sentry')) {
                app('sentry')->captureException($exception);
            }
            return view("errors.500", ["exception"=>$exception]);
        }

         try {
            $launch = LtiMessageLaunch::new(
                new \App\Library\LTI13Database, 
                new \App\Library\LTI13Cache, 
                new \App\Library\LTI13Cookie, 
                new \Packback\Lti1p3\LtiServiceConnector(
                    new \App\Library\LTI13Cache, 
                    new \GuzzleHttp\Client([
                        'timeout' => 30,
                    ])
                )
            )
            ->validate();
        }
        catch (LtiException $e) {

            // canvas needs to update for new window to work https://github.com/instructure/canvas-lms/commit/811a1194cabccc1b3fb22aa3d13d64cde547116d#diff-79b6cd1bab1e82354966238b3d72cfa8fffb6357a61d2454bf4aba1c85b96a5e
//             echo '<script>
//             window.parent.postMessage(
//   {
//     messageType: "requestFullWindowLaunch",
//     data: {
//       url: "https://cla-chimein-dev.oit.umn.edu/lti13/launch",
//       launchType: "new_window",
//     }
//   },
//   "*"
// )           
            // </script>';
            echo '<script>
            window.parent.postMessage(
  {
    messageType: "requestFullWindowLaunch",
    data: "' . url("lti13/launch") . '"
  },
  "*"
)           
            </script>
            <h1>Canvas Launch Error</h1>
            <p>' . $e->getMessage() . "</p>";
            return;
        }
        session(['lti_launch' => true]);
        $launchData = $launch->getLaunchData();

        $lisData = $launchData["https://purl.imsglobal.org/spec/lti/claim/lis"];
        $rolesData = $launchData["https://purl.imsglobal.org/spec/lti/claim/roles"];
        $contextData = $launchData["https://purl.imsglobal.org/spec/lti/claim/context"];
        $endpointData = $launchData["https://purl.imsglobal.org/spec/lti-ags/claim/endpoint"];
        $resourceData = $launchData["https://purl.imsglobal.org/spec/lti/claim/resource_link"];

        $presentationData = $launchData["https://purl.imsglobal.org/spec/lti/claim/launch_presentation"];

        $returnURL = explode("external_content", $presentationData["return_url"])[0];
        // LTI1.3 always passes us "instructure.com" domained urls. We don't really want that because it 
        // creates cookie issues potentially if users access ChimeIn in an iframe from a umn.instructure url.
        // This isn't a great universal fix obviously - the real fix is a change in the U's Canvas config
        $returnURL = str_replace("umn.instructure.com", "canvas.umn.edu", $returnURL);
        
        $resourceLinks = LTI13ResourceLink::where("resource_link", $resourceData["id"])->get();    
        if($resourceLinks->count() > 0) {
            $resourceLink = $resourceLinks->first();
        }
        else {

            $deploymentId = $launchData['https://purl.imsglobal.org/spec/lti/claim/deployment_id'];
            try {
                $deployment = \App\Models\LTI13Deployment::where("deployment_id", $deploymentId)->firstOrFail();
            }
            catch (ModelNotFoundException $ex) {
                Log::error("Model not found launching from Canvas", $ex);
                return view("errors.500", ["exception"=>$ex]);
            }
            

            $resourceLink = new Lti13ResourceLink;
            $resourceLink->resource_link = $resourceData["id"];
            $resourceLink->endpoint = $endpointData;
            $resourceLink->deployment_id = $deployment->id;
            $resourceLink->save();
        }
        
        $lisData = $this->mungeLisData($lisData);

        if(!$lisData["person_sourcedid"] || !is_numeric($lisData["person_sourcedid"])) {
            return view("errors.emplid");
        }
        
        

        if(Auth::attempt(["emplid"=>$lisData["person_sourcedid"]]) || Auth::attempt(["email"=>$launchData["email"]])) {
            Auth::user()->lti13_sub_id = $launchData["sub"];
            Auth::user()->save();
        }
        else {
            $user = new \App\Models\User;
            $user->email =$launchData["email"];
            $user->name =$launchData["name"];
            $user->emplid = $lisData["person_sourcedid"];
            $user->lti13_sub_id = $launchData["sub"];
            $user->save();
            Auth::login($user);
        }

        if(count(array_intersect($rolesData, $this->staffRoles)) > 0) {
             dd("You're an instructor!");
        }
        else {
            dd("You're a student!");
        }

    }


    public function config(Request $request) {
        // key generated with https://mkjwk.org
        $configArray = [
            "title" => "SmartyCards",
            "description" => "SmartyCards",
            "oidc_initiation_url" => url("lti13/login"),
            "target_link_uri" => url("lti13/launch"),
            "scopes" => [
                "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
                "https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
                "https://purl.imsglobal.org/spec/lti-ags/scope/score"
            ],
            "extensions" => [
                [
                    "domain" => $request->getHost(),
                    "tool_id" => "smartycards",
                    "platform" => "canvas.instructure.com",
                    "privacy_level" => "public",
                    "settings" => [
                        "text" => "Launch SmartyCards",
                        "icon_url" => url("/library/images/home/record-icon.png"),
                        "placements" => [
                                [
                                    "text" => "SmartyCards",
                                    "enabled" => true,
                                    "placement" => "assignment_selection",
                                    "message_type" => "LtiResourceLinkRequest",
                                    "target_link_uri" => url("lti13/launch"),
                                    "canvas_icon_class" => "icon-lti"
                                ]
                            ]
                        ]
                    ]
            ],
            "public_jwk" => [
                "kty"=> "RSA",
                "e"=> "AQAB",
                "use"=> "sig",
                "kid"=>"sig-1756313423",
                "alg"=>"RS256",
                "n"=> "tHROyFOAeRBZhJGDqrqnA8-NLBvqMszCo9-mNDqSWfoSO7fhs5sF80kVglKdVYhmVZsaBCTM-A76O-tJ4BU0r1eFKIW54CCiHxJzPuCN5KdFEkzBD7FKP2otW9JEsIG1xFhMcQGGEAf31dEisTvPZUOQOAk6gC8WrUwyQKwYb6Ds-2GaQbvPIcqK6xIya0AW3D3rG0tryzF-UY-PWU0m6QfhXA0hZrltw_jMGOQuMjmy80KCKeArR5ybWbwroLQdrqjF4w8tYvwidwsBc2Fyi-02d6AZnsYmXz31yWZ1IjdmLVe-QAMidoV8dukwFaxj3m5I8uS_mfSgEwDRKoXE6Q"

            ],
            "custom_fields" => [  
                "canvas_integration_id" =>'$Canvas.user.sisSourceId',
                "user_username" => '$User.username',
                "canvas_user_id" => '$Canvas.user.id',
                "canvas_course_id" => '$Canvas.course.id'
            ]
        ];

        return response()->json($configArray);
    }

     private function mungeLisData($lisData) {
        if($lisData["person_sourcedid"]== "SISIDformcfa0086") {
            $lisData["person_sourcedid"] = 2328381;
        }
        // our dev instance sends this value. need to make it a real emplid (elevator internetID)
        if($lisData["person_sourcedid"] == "SISID4elevator" || $lisData["person_sourcedid"]  == "Dx7a7sg9zz") {
            $lisData["person_sourcedid"] = 1111111;
        }
        // latistecharch internetID
        if($lisData["person_sourcedid"]  == "D95saru5c2") {
            $lisData["person_sourcedid"] = 1111113;
        }
        // our dev instance sends this value. need to make it a real emplid
        if($lisData["person_sourcedid"] == "emplidFORjohnsojr") {
            $lisData["person_sourcedid"] = 1111112;
        }
        return $lisData;
    }
}
