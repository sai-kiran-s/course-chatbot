<?php

namespace App\Conversations;
require_once 'functions.php';
use BotMan\BotMan\Messages\Conversations\Conversation;
$botman = resolve('botman');

use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class postLoginConversation extends Conversation
{
    // public function __construct() {
    //     $this->say('Hello!');
    // }
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    
    public function run()
    {

        $question = Question::create('What do you wish to do?') ->addButtons([
            Button::create('Have a problem, wish to solve it')->value('problem'),
            Button::create('Have an enquiry to make')->value('enquiry'),
        ]);
        $this->ask($question,function ($answer){
            if($answer->isInteractiveMessageReply()){
                if($answer->getValue() == 'problem'){
                    $this->ask('Oh! We are sorry for the inconvenience, could you brief your problem?',function ($answer){
                        switch($answer){
                            case 'How to reset a lost or forgotten password': 
                                $this->say('From the sign in page, click Forgot password? Enter your email address and request for OTP. Check your email inbox. The email will include an OTP. Enter this OTP into the text field on the recover password page, choose a new password and click Recover Password.');
                                $question = Question::create('Do you have another Query??') ->addButtons([
                                    Button::create('YES')->value('yes'),
                                    Button::create('NO')->value('no'),
                                ]);
                                $this->ask($question,function ($answer){
                                    if($answer->getValue() == 'yes'){
                                        $this->run();
                                    }
                                });
                                break;
                            case 'I requested a new password reset, but it has not arrived in my email inbox.': 
                                $this->say('Be sure to check your junk or spam filters on your email inbox');
                                $question = Question::create('Do you have another Query??') ->addButtons([
                                    Button::create('YES')->value('yes'),
                                    Button::create('NO')->value('no'),
                                ]);
                                $this->ask($question,function ($answer){
                                    if($answer->getValue() == 'yes'){
                                        $this->run();
                                    }
                                });
                                break;
                            case 'I am receiving the password reset emails but I still cannot log in!':
                                $this->say('The links in those emails are time-sensitive if not try logging in from a different browser');
                                $question = Question::create('Do you have another Query??') ->addButtons([
                                    Button::create('YES')->value('yes'),
                                    Button::create('NO')->value('no'),
                                ]);
                                $this->ask($question,function ($answer){
                                    if($answer->getValue() == 'yes'){
                                        $this->run();
                                    }
                                });
                                break;
                        }
                    });
                    
                }
                else{
                    
                    $this->ask('We are happy to resolve your query, could you brief your query?',function ($answer){
                        
                        if(strpos(strtolower($answer),'all courses')!==false && (strpos(strtolower($answer),'dynamics 365')!==false||strpos($answer,'microsoft 365')!==false||strpos($answer,'azure')!==false||strpos($answer,'power platform')!==false||strpos($answer,'security')!==false||strpos($answer,'aws')!==false||strpos($answer,'gcp')!==false)){
                                $ids = array();
                                $result = allCourseMapper($answer);
                                foreach ($result as $key) {
                                    array_push($ids,$key[0]);
                                }
                                $this->say("Course IDs: 
                                ".implode(",",$ids));
                                
                        }else if(strpos(strtolower($answer),'associate')!==false||strpos(strtolower($answer),'expert')!==false||strpos(strtolower($answer),'foundational')!==false){
                            
                            $setOfCourses = findCoursesBasedOnDifficulty($answer);
                            
                            $this->say("Course Name: ".implode(", ",$setOfCourses));
                        }else if((strpos(strtolower($answer),'details')!==false)&&(preg_match('/[A-Za-z] [0-9][0-9][0-9]/', strtolower($answer))||strpos(strtolower($answer),'soa-c01')!==false||strpos(strtolower($answer),'pca')!==false)){
                            $courseName = findCoursesBasedOnId($answer);
                            
                            $this->say("Course Name: ".$courseName[1]);
                        }else if((strpos(strtolower($answer),'skills')!==false)&&(preg_match('/[A-Za-z] [0-9][0-9][0-9]/', strtolower($answer))||strpos(strtolower($answer),'soa-c01')!==false||strpos(strtolower($answer),'pca')!==false)){
                            $ids = array();
                            $courseSkills = findSkillsBasedOnId($answer);
                            $i=0;
                            $this->say("Skills: ");
                            foreach ($courseSkills as $key) {
                                if($i!=0 && $i%2 == 0){
                                    $this->say(implode(" - ",$ids));
                                    $ids = array();
                                    array_push($ids,$key);
                                }
                                else array_push($ids,$key);
                                $i+=1;
                            }
                        }else{
                            
                        }
                        $question = Question::create('Do you have another Query??') ->addButtons([
                            Button::create('YES')->value('yes'),
                            Button::create('NO')->value('no'),
                        ]);
                        $this->ask($question,function ($answer){
                            if($answer->getValue() == 'yes'){
                                $this->run();
                            }
                        });
                        
                    });
                }
                
            }
            else{
                $this->repeat();
            }

        });  
    }
    public function askAge(){
        $this->ask('What\'s your age?',function ($answer){
            if(trim($answer->getText())===''){
                return $this->repeat('This does not look like a invalid age. Please enter your age..');
             }
            $this->age = $answer->getText();
            $this->say('You are '.$this->age);     
        });  
    }
}