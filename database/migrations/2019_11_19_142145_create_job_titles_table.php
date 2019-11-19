<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\JobTitle;

class CreateJobTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_titles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->timestamps();
        });
        // $this->createData();
    }

    // public function createData(){
    //   $titles = [
    //     "Marketing Specialist","Marketing Manager","Marketing Director","Graphic Designer","Marketing Research Analyst","Marketing Communications Manager","Marketing Consultant","Product Manager","Public Relations","Social Media Assistant","Brand Manager","SEO Manager","Content Marketing Manager","Copywriter","Media Buyer","Digital Marketing Manager","eCommerce Marketing Specialist","Brand Strategist","Vice President of Marketing","Media Relations Coordinator","Self Employed","Unemployed",
    //     "Rather not say","Stock Trader","Trader","Forex Trader","Technical Analyst","Analyst","Backend Developer ","Developer","Frontend Developer","Pastor","Prophet","Apostle","Native Doctor","Administrative Assistant","Receptionist","Office Manager","Auditing Clerk","Bookkeeper","Account Executive","Branch Manager","Business Manager","Quality Control Coordinator","Administrative Manager","Chief Executive Officer","Business Analyst","Risk Manager","Human Resources",
    //     "Office Assistant","Secretary","Office Clerk","File Clerk","Account Collector","Administrative Specialist","Executive Assistant","Program Administrator","Program Manager","Administrative Analyst","Data Entry","CEO—Chief Executive Officer","COO—Chief Operating Officer","CFO—Chief Financial Officer","CIO—Chief Information Officer","CTO—Chief Technology Officer","CMO—Chief Marketing Officer","CHRO—Chief Human Resources Officer","CDO—Chief Data Officer",
    //     "CPO—Chief Product Officer","CCO—Chief Customer Officer","Team Leader","Manager","Assistant Manager","Executive","Director","Coordinator","Administrator","Controller","Officer","Organizer","Supervisor","Superintendent","Head","Overseer","Chief","Foreman","Controller","Principal","","President","Lead","Delivery Driver","School Bus Driver","Truck Driver","Tow Truck Operator","UPS Driver","Mail Carrier","Recyclables Collector","Courier","Bus Driver","Cab Driver",
    //     "Animal Shelter Board Member","Office Volunteer","Animal Shelter Volunteer","Hospital Volunteer","Youth Volunteer","Food Kitchen Worker","Homeless Shelter Worker","Conservation Volunteer","Meals on Wheels Driver","Habitat for Humanity Builder","Emergency Relief Worker","Red Cross Volunteer","Community Food Project Worker",
    //     "Women's Shelter Jobs","Suicide Hotline Volunteer","School Volunteer","Community Volunteer Jobs","Sports Volunteer","Church Volunteer","Archivist","Actuary","Architect","Personal Assistant","Entrepreneur","Security Guard","Mechanic","Recruiter","Mathematician","Locksmith","Management Consultant","Shelf Stocker","Caretaker or House Sitter","Library Assistant","Translator","HVAC Technician","Attorney","Paralegal","Executive Assistant","Personal Assistant","Bank Teller",
    //     "Parking Attendant","Machinery Operator","Manufacturing Assembler","Funeral Attendant","Assistant Golf Professional","Yoga Instructor","Massage Terapist ","Commercial Sex Worker","Gym instructor","Politician","","Computer Scientist","IT Professional","UX Designer & UI Developer","SQL Developer","Web Designer","Web Developer","Help Desk Worker/Desktop Support","Software Engineer","Data Entry","DevOps Engineer","Computer Programmer","Network Administrator",
    //     "Information Security Analyst","Artificial Intelligence Engineer","Cloud Architect","IT Manager","Technical Specialist","Application Developer","Chief Technology Officer (CTO)","Chief Information Officer (CIO)","","Retail Worker","Store Manager","Sales Representative","Sales Manager","Real Estate Broker","Sales Associate","Cashier","Store Manager","Account Executive","Account Manager","Area Sales Manager","Direct Salesperson","Director of Inside Sales","Outside Sales Manager",
    //     "Sales Analyst","Market Development Manager","B2B Sales Specialist","Sales Engineer","Construction Worker","Taper","Plumber","Heavy Equipment Operator","Vehicle or Equipment Cleaner","Carpenter","Electrician","Painter","Welder","Handyman","Boilermaker","Crane Operator","Building Inspector","Pipefitter","Sheet Metal Worker","Iron Worker","Mason","Roofer","Solar Photovoltaic Installer","Well Driller","Credit Authorizer","Benefits Manager",
    //     "Credit Counselor","Accountant","Bookkeeper","Accounting Analyst","Accounting Director","Accounts Payable/Receivable Clerk","Auditor","Budget Analyst","Controller","Financial Analyst","Finance Manager","Economist","Payroll Manager","Payroll Clerk","Financial Planner","Financial Services Representative","Finance Director","Commercial Loan Officer","Engineer","Mechanical Engineer","Civil Engineer","Electrical Engineer","Assistant Engineer","Chemical Engineer",
    //     "Chief Engineer","Drafter","Engineering Technician","Geological Engineer","Biological Engineer","Maintenance Engineer","Mining Engineer","Nuclear Engineer","Petroleum Engineer","Plant Engineer","Production Engineer","Quality Engineer","Safety Engineer","Sales Engineer","Researcher","Research Assistant","Data Analyst","Business Analyst","Financial Analyst","Biostatistician","Title Researcher","Market Researcher","Title Analyst","Medical Researcher","Researcher",
    //     "Research Assistant","Data Analyst","Business Analyst","Financial Analyst","Biostatistician","Title Researcher","Market Researcher","Title Analyst","Medical Researcher","Graphic Designer","Artist","Interior Designer","Video Editor","Video or Film Producer","Playwright","Musician","Novelist/Writer","Computer Animator","Photographer","Camera Operator","Sound Engineer","Motion Picture Director","Actor","Music Producer","Director of Photography",
    //     "Nurse","Travel Nurse","Nurse Practitioner","Doctor","Caregiver","CNA","Physical Therapist","Pharmacist","Pharmacy Assistant","Medical Administrator","Medical Laboratory Tech","Physical Therapy Assistant","Massage Therapy","Dental Hygienist","Orderly","Personal Trainer","Massage Therapy","Medical Laboratory Tech","Phlebotomist","Medical Transcriptionist","Telework Nurse/Doctor","Reiki Practitioner","Housekeeper","Flight Attendant","Travel Agent",
    //     "Hotel Front Door Greeter","Bellhop","Cruise Director","Entertainment Specialist","Hotel Manager","Front Desk Associate","Front Desk Manager","Concierge","Group Sales","Event Planner","Porter","Spa Manager","Wedding Coordinator","Cruise Ship Attendant","Casino Host","Hotel Receptionist","Reservationist","Events Manager","Meeting Planner","Lodging Manager","Director of Maintenance","Valet","Waiter/Waitress","Server","Chef","Fast Food Worker","Barista","Line Cook",
    //     "Cafeteria Worker","Restaurant Manager","Wait Staff Manager","Bus Person","Restaurant Chain Executive","","Political Scientist","Chemist","Conservation Scientist","Sociologist","Biologist","Geologist","Physicist","Astronomer","Atmospheric Scientist","Molecular Scientist","","Call Center Representative","Customer Service","Telemarketer","Telephone Operator","Phone Survey Conductor","Dispatcher for Trucks or Taxis","Customer Support Representative","Over the Phone Interpreter",
    //     "Phone Sales Specialist","Mortgage Loan Processor","Counselor","Mental Health Counselor","Addiction Counselor","School Counselor","Speech Pathologist","Guidance Counselor","Social Worker","Therapist","Life Coach","Couples Counselor","Beautician","Hair Stylist","Nail Technician","Cosmetologist","Salon Manager","Makeup Artist","Esthetician","Skin Care Specialist","Manicurist","Barber","FASHION DESIGNER","MODDEL","Journalist","Copy Editor","Editor/Proofreader",
    //     "Content Creator","Speechwriter","Communications Director","Screenwriter","Technical Writer","Columnist","Public Relations Specialist","Proposal Writer","Content Strategist","Grant Writer","Video Game Writer","Translator","Film Critic","Copywriter","Travel Writer","Social Media Specialist","Ghostwriter","Warehouse Worker","Painter","Truck Driver","Heavy Equipment Operator","Welding","Physical Therapy Assistant","Housekeeper","Landscaping Worker","Landscaping Assistant",
    //     "Mover","Animal Breeder","Veterinary Assistant","Farm Worker","Animal Shelter Worker","Dog Walker / Pet Sitter","Zoologist","Animal Trainer","Service Dog Trainer","Animal Shelter Manager","Animal Control Officer"
    //   ];
    //
    //   foreach ($titles as $key => $title) {
    //     try {
    //       JobTitle::create(['name' => $title]);
    //     } catch (\Exception $e) {
    //       print($e->getMessage());
    //     }
    //   }
    // }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_titles');
    }
}
