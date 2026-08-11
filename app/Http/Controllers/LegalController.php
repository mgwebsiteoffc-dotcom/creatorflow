<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function terms()             { return view('marketing.legal.terms'); }
    public function privacy()           { return view('marketing.legal.privacy'); }
    public function refund()            { return view('marketing.legal.refund'); }
    public function cookies()           { return view('marketing.legal.cookies'); }
    public function shipping()          { return view('marketing.legal.shipping'); }
    public function content()           { return view('marketing.legal.content'); }
    public function creatorAgreement()  { return view('marketing.legal.creator-agreement'); }
}
