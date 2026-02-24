<?php

namespace Database\Seeders;

use App\Models\AdmissionTerm;
use App\Models\AreaOfInterest;
use App\Models\College;
use App\Models\Concentration;
use App\Models\FeaturedCourse;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\ProgramDeadline;
use App\Models\ProgramRequirement;
use App\Models\ProgramType;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        // Lookup taxonomy records by name
        $types    = ProgramType::pluck('id', 'name');
        $colleges = College::pluck('id', 'name');
        $areas    = AreaOfInterest::pluck('id', 'name');
        $terms    = AdmissionTerm::pluck('id', 'name');

        $programs = [

            // 1 ─ MS Data Science
            [
                'title'                  => 'MS in Data Science',
                'program_name'           => 'Master of Science in Data Science',
                'slug'                   => 'ms-data-science',
                'program_type'           => "Master's",
                'degree_coursework_name' => 'Master of Science',
                'program_major'          => 'Data Science',
                'college'                => 'College of Engineering',
                'program_format'         => 'asynchronous',
                'duration'               => '2 years',
                'credit_hours'           => 36,
                'program_fees'           => 18500.00,
                'is_active'              => true,
                'overview'               => '<p>The Master of Science in Data Science equips students with the analytical tools, machine learning techniques, and statistical foundations needed to extract actionable insights from complex datasets. Our fully online, asynchronous program is designed for working professionals who want to advance their careers without interrupting them.</p><p>Students gain hands-on experience with Python, R, SQL, and cloud-based data platforms through project-based coursework developed in partnership with industry leaders.</p>',
                'terms'                  => ['Fall', 'Spring'],
                'areas'                  => ['Computer Science', 'Computer Science & Data Technology', 'Engineering'],
                'requirements'           => [
                    'Bachelor\'s degree from an accredited institution with a minimum GPA of 3.0',
                    'Two letters of professional recommendation',
                    'Statement of purpose (500–750 words)',
                    'Official transcripts from all prior institutions',
                    'Proficiency in at least one programming language (Python or R preferred)',
                ],
                'concentrations'         => [
                    ['name' => 'Machine Learning', 'description' => 'Deep dive into supervised and unsupervised learning, neural networks, and model deployment pipelines.'],
                    ['name' => 'Business Analytics', 'description' => 'Apply data science methods to real-world business problems including forecasting, customer segmentation, and risk modeling.'],
                ],
                'courses'                => [
                    ['title' => 'Foundations of Machine Learning', 'description' => 'Core algorithms including regression, classification, clustering, and evaluation metrics.'],
                    ['title' => 'Big Data Engineering', 'description' => 'Distributed computing with Spark, Hadoop, and cloud data warehouses.'],
                    ['title' => 'Applied Statistics for Data Scientists', 'description' => 'Statistical inference, hypothesis testing, and Bayesian methods.'],
                ],
                'deadlines'              => ['Fall' => '2026-05-01', 'Spring' => '2025-10-15'],
                'testimonials'           => [
                    [
                        'student_name'    => 'Priya Sharma',
                        'graduation_year' => '2024',
                        'program_taken'   => 'MS in Data Science',
                        'testimonial'     => 'The program completely transformed my career. I went from a junior analyst to a senior data scientist at a Fortune 500 company within a year of graduating. The asynchronous format made it possible to study while holding a full-time job.',
                    ],
                ],
                'faculty'                => [
                    [
                        'name'           => 'Dr. James Thornton',
                        'department'     => 'Computer Science & Engineering',
                        'courses_taught' => 'Machine Learning, Deep Learning',
                        'description'    => 'Dr. Thornton brings 15 years of industry experience at Google and Amazon before joining academia. His research focuses on scalable ML systems.',
                    ],
                ],
            ],

            // 2 ─ MBA
            [
                'title'                  => 'Online MBA',
                'program_name'           => 'Master of Business Administration',
                'slug'                   => 'online-mba',
                'program_type'           => "Master's",
                'degree_coursework_name' => 'Master of Business Administration',
                'program_major'          => 'Business Administration',
                'college'                => 'College of Business',
                'program_format'         => 'asynchronous',
                'duration'               => '18 months',
                'credit_hours'           => 48,
                'program_fees'           => 24000.00,
                'is_active'              => true,
                'overview'               => '<p>The Online MBA from the College of Business is ranked among the top programs in the nation for flexibility and career outcomes. Designed for mid-career professionals, the program develops strategic leadership, financial acumen, and global business perspective.</p><p>Students collaborate with a cohort of ambitious peers from diverse industries, gaining the network and credentials to accelerate into executive roles.</p>',
                'terms'                  => ['Fall', 'Spring', 'Summer'],
                'areas'                  => ['Business', 'Business Law & Leadership'],
                'requirements'           => [
                    'Bachelor\'s degree with a minimum GPA of 2.75',
                    'Minimum 2 years of post-undergraduate work experience',
                    'GMAT or GRE scores (waiver available for applicants with 5+ years experience)',
                    'Resume or CV',
                    'Two professional references',
                    'Video interview with admissions committee',
                ],
                'concentrations'         => [
                    ['name' => 'Finance & Investment', 'description' => 'Advanced corporate finance, portfolio management, and financial modeling.'],
                    ['name' => 'Healthcare Management', 'description' => 'Strategy and operations for healthcare organizations in a rapidly changing regulatory environment.'],
                    ['name' => 'Entrepreneurship', 'description' => 'Startup strategy, venture finance, and innovation management for aspiring founders.'],
                ],
                'courses'                => [
                    ['title' => 'Managerial Economics', 'description' => 'Economic principles applied to business decision-making and competitive strategy.'],
                    ['title' => 'Corporate Finance', 'description' => 'Capital structure, valuation, mergers & acquisitions, and risk management.'],
                    ['title' => 'Global Strategy', 'description' => 'Competing in international markets, cross-border operations, and geopolitical risk.'],
                ],
                'deadlines'              => ['Fall' => '2026-06-01', 'Spring' => '2025-11-01', 'Summer' => '2026-02-15'],
                'testimonials'           => [
                    [
                        'student_name'    => 'Marcus Williams',
                        'graduation_year' => '2023',
                        'program_taken'   => 'Online MBA',
                        'testimonial'     => 'The MBA opened doors I didn\'t even know existed. I was promoted to VP of Operations six months after completing the program. The finance concentration gave me the language to speak credibly with the board.',
                    ],
                    [
                        'student_name'    => 'Elena Vasquez',
                        'graduation_year' => '2024',
                        'program_taken'   => 'Online MBA',
                        'testimonial'     => 'Balancing the program with two kids and a demanding job was tough but absolutely worth it. The cohort model meant I always had people to lean on.',
                    ],
                ],
                'faculty'                => [
                    [
                        'name'           => 'Dr. Patricia Langford',
                        'department'     => 'Finance & Accounting',
                        'courses_taught' => 'Corporate Finance, Investment Strategy',
                        'description'    => 'Former CFO with experience at Goldman Sachs and PwC. Dr. Langford brings real-world case studies directly from her 20-year career in finance.',
                    ],
                ],
            ],

            // 3 ─ BSN
            [
                'title'                  => 'RN to BSN',
                'program_name'           => 'Bachelor of Science in Nursing (RN to BSN)',
                'slug'                   => 'rn-to-bsn',
                'program_type'           => 'Bachelor',
                'degree_coursework_name' => 'Bachelor of Science in Nursing',
                'program_major'          => 'Nursing',
                'college'                => 'College of Nursing',
                'program_format'         => 'asynchronous',
                'duration'               => '12–18 months',
                'credit_hours'           => 30,
                'program_fees'           => 9500.00,
                'is_active'              => true,
                'overview'               => '<p>Designed exclusively for licensed registered nurses, the RN to BSN completion program builds on your clinical expertise with advanced coursework in leadership, community health, evidence-based practice, and patient-centered care.</p><p>All coursework is delivered online with no campus visits required, making it the most convenient path to the BSN credential that many hospital employers now require or prefer.</p>',
                'terms'                  => ['Fall', 'Spring', 'Summer'],
                'areas'                  => ['Health Sciences'],
                'requirements'           => [
                    'Current, unrestricted RN license in the U.S.',
                    'Associate Degree in Nursing (ADN) or nursing diploma from an accredited program',
                    'Minimum cumulative GPA of 2.5',
                    'Official transcripts',
                    'Copy of current RN license',
                ],
                'concentrations'         => [],
                'courses'                => [
                    ['title' => 'Evidence-Based Practice in Nursing', 'description' => 'Research methodology and applying clinical evidence to improve patient outcomes.'],
                    ['title' => 'Nursing Leadership & Management', 'description' => 'Healthcare systems, organizational theory, and nursing management skills.'],
                    ['title' => 'Community and Population Health', 'description' => 'Social determinants of health, epidemiology, and public health nursing.'],
                ],
                'deadlines'              => ['Fall' => '2026-07-15', 'Spring' => '2025-11-30', 'Summer' => '2026-03-01'],
                'testimonials'           => [
                    [
                        'student_name'    => 'Angela Torres',
                        'graduation_year' => '2024',
                        'program_taken'   => 'RN to BSN',
                        'testimonial'     => 'I completed this program while working night shifts at the hospital. The flexibility was incredible. My manager noticed the change in how I approached patient care almost immediately.',
                    ],
                ],
                'faculty'                => [
                    [
                        'name'           => 'Dr. Sandra Kim',
                        'department'     => 'College of Nursing',
                        'courses_taught' => 'Evidence-Based Practice, Community Health Nursing',
                        'description'    => 'Dr. Kim is a certified Family Nurse Practitioner with clinical experience in rural and underserved community health settings.',
                    ],
                ],
            ],

            // 4 ─ JD
            [
                'title'                  => 'Juris Doctor (JD)',
                'program_name'           => 'Juris Doctor',
                'slug'                   => 'juris-doctor',
                'program_type'           => 'Doctorate',
                'degree_coursework_name' => 'Juris Doctor',
                'program_major'          => 'Law',
                'college'                => 'College of Law',
                'program_format'         => 'synchronous',
                'duration'               => '3 years',
                'credit_hours'           => 90,
                'program_fees'           => 32000.00,
                'is_active'              => true,
                'overview'               => '<p>The Juris Doctor program is one of the most respected law degrees in the region, consistently producing graduates who pass the bar exam at rates above the national average. Our curriculum emphasizes rigorous legal reasoning, oral advocacy, and practical skills through clinics and moot court competitions.</p><p>Students choose from a range of specialty tracks including business law, public interest law, and intellectual property.</p>',
                'terms'                  => ['Fall'],
                'areas'                  => ['Law', 'Business Law & Leadership'],
                'requirements'           => [
                    'Bachelor\'s degree from an accredited institution',
                    'LSAT score of 155 or higher (or GRE equivalent)',
                    'Personal statement (two pages)',
                    'Two letters of recommendation from academic or professional sources',
                    'Resume demonstrating leadership or community involvement',
                    'Official transcripts',
                ],
                'concentrations'         => [
                    ['name' => 'Business & Corporate Law', 'description' => 'Transactional law, corporate governance, securities regulation, and M&A.'],
                    ['name' => 'Public Interest & Social Justice', 'description' => 'Civil rights, immigration, environmental law, and nonprofit advocacy.'],
                ],
                'courses'                => [
                    ['title' => 'Constitutional Law', 'description' => 'Fundamental rights, separation of powers, and landmark Supreme Court jurisprudence.'],
                    ['title' => 'Contracts & Commercial Law', 'description' => 'Formation, performance, breach, and remedies under the UCC and common law.'],
                    ['title' => 'Legal Research & Writing', 'description' => 'Objective and persuasive legal writing, brief drafting, and research methodology.'],
                ],
                'deadlines'              => ['Fall' => '2026-03-01'],
                'testimonials'           => [
                    [
                        'student_name'    => 'David Okonkwo',
                        'graduation_year' => '2023',
                        'program_taken'   => 'Juris Doctor',
                        'testimonial'     => 'The clinical program gave me real client experience before I even graduated. I walked into my first job at a public defender\'s office having already handled actual cases. There\'s no substitute for that.',
                    ],
                ],
                'faculty'                => [
                    [
                        'name'           => 'Prof. Richard Hale',
                        'department'     => 'College of Law',
                        'courses_taught' => 'Constitutional Law, Civil Procedure',
                        'description'    => 'Former law clerk for the U.S. Court of Appeals and practicing litigator for 12 years before joining the faculty. Author of two casebooks on federal civil procedure.',
                    ],
                ],
            ],

            // 5 ─ MSW
            [
                'title'                  => 'Master of Social Work',
                'program_name'           => 'Master of Social Work',
                'slug'                   => 'master-of-social-work',
                'program_type'           => "Master's",
                'degree_coursework_name' => 'Master of Social Work',
                'program_major'          => 'Social Work',
                'college'                => 'College of Social Work',
                'program_format'         => 'hybrid',
                'duration'               => '2 years',
                'credit_hours'           => 60,
                'program_fees'           => 14000.00,
                'is_active'              => true,
                'overview'               => '<p>The Master of Social Work prepares students for advanced clinical practice and leadership in social services, mental health, child welfare, and community development. Our CSWE-accredited program combines rigorous online coursework with a 900-hour field practicum placed in agencies throughout the region.</p>',
                'terms'                  => ['Fall', 'Spring'],
                'areas'                  => ['Social Work', 'Health Sciences', 'Education & Human Development'],
                'requirements'           => [
                    'Bachelor\'s degree (any major) from an accredited institution',
                    'Minimum GPA of 2.8',
                    'Personal statement describing social work experience and goals',
                    'Three letters of recommendation',
                    'Official transcripts',
                    'Background check (required prior to field placement)',
                ],
                'concentrations'         => [
                    ['name' => 'Clinical Mental Health', 'description' => 'Advanced clinical assessment, therapy modalities, and trauma-informed care.'],
                    ['name' => 'Child, Youth & Family Services', 'description' => 'Child welfare, foster care systems, family preservation, and juvenile justice.'],
                ],
                'courses'                => [
                    ['title' => 'Human Behavior in the Social Environment', 'description' => 'Ecological systems theory and biopsychosocial frameworks for understanding human development.'],
                    ['title' => 'Social Work Practice with Individuals & Families', 'description' => 'Assessment, intervention planning, and evidence-based treatment approaches.'],
                    ['title' => 'Social Policy & Advocacy', 'description' => 'Policy analysis, legislative advocacy, and the social worker\'s role in systems change.'],
                ],
                'deadlines'              => ['Fall' => '2026-02-01', 'Spring' => '2025-09-15'],
                'testimonials'           => [
                    [
                        'student_name'    => 'Keisha Johnson',
                        'graduation_year' => '2024',
                        'program_taken'   => 'Master of Social Work',
                        'testimonial'     => 'The field practicum placed me at exactly the agency I wanted to work at after graduation — and they offered me a job. The faculty genuinely invested in my growth as a clinician.',
                    ],
                ],
                'faculty'                => [
                    [
                        'name'           => 'Dr. Monica Wells',
                        'department'     => 'College of Social Work',
                        'courses_taught' => 'Clinical Practice, Trauma-Informed Care',
                        'description'    => 'Licensed Clinical Social Worker with 18 years of direct practice in community mental health. Dr. Wells conducts research on trauma interventions for marginalized youth populations.',
                    ],
                ],
            ],

            // 6 ─ MEd
            [
                'title'                  => 'MEd in Instructional Technology',
                'program_name'           => 'Master of Education in Instructional Technology',
                'slug'                   => 'med-instructional-technology',
                'program_type'           => "Master's",
                'degree_coursework_name' => 'Master of Education',
                'program_major'          => 'Instructional Technology',
                'college'                => 'College of Education',
                'program_format'         => 'asynchronous',
                'duration'               => '2 years',
                'credit_hours'           => 33,
                'program_fees'           => 11500.00,
                'is_active'              => true,
                'overview'               => '<p>The MEd in Instructional Technology prepares educators, trainers, and learning designers to create engaging, effective digital learning experiences. Graduates work in K–12 schools, higher education, corporate training departments, and instructional design firms.</p>',
                'terms'                  => ['Fall', 'Spring'],
                'areas'                  => ['Education & Human Development', 'Computer Science'],
                'requirements'           => [
                    'Bachelor\'s degree in education or a related field',
                    'Minimum GPA of 3.0',
                    'Statement of professional goals',
                    'Two letters of recommendation',
                    'Current résumé',
                ],
                'concentrations'         => [
                    ['name' => 'eLearning Design', 'description' => 'Authoring tools (Articulate, Captivate), storyboarding, and learner experience design.'],
                    ['name' => 'K–12 Technology Integration', 'description' => 'Curriculum alignment, LMS administration, and classroom technology coaching.'],
                ],
                'courses'                => [
                    ['title' => 'Learning Theory & Design', 'description' => 'Cognitivism, constructivism, and their application to instructional design models.'],
                    ['title' => 'Multimedia Design for Learning', 'description' => 'Cognitive load theory, visual design principles, and accessible media creation.'],
                    ['title' => 'Data-Driven Instruction', 'description' => 'Learning analytics, assessment design, and using data to improve instructional outcomes.'],
                ],
                'deadlines'              => ['Fall' => '2026-06-01', 'Spring' => '2025-10-01'],
                'testimonials'           => [],
                'faculty'                => [
                    [
                        'name'           => 'Dr. Alicia Freeman',
                        'department'     => 'College of Education',
                        'courses_taught' => 'Learning Theory, Multimedia Design',
                        'description'    => 'Award-winning instructional designer with experience at IBM and Coursera before joining the faculty. Expert in accessibility and universal design for learning.',
                    ],
                ],
            ],

            // 7 ─ DBA
            [
                'title'                  => 'Doctor of Business Administration',
                'program_name'           => 'Doctor of Business Administration',
                'slug'                   => 'doctor-of-business-administration',
                'program_type'           => 'Doctorate',
                'degree_coursework_name' => 'Doctor of Business Administration',
                'program_major'          => 'Business Administration',
                'college'                => 'College of Business',
                'program_format'         => 'hybrid',
                'duration'               => '3 years',
                'credit_hours'           => 60,
                'program_fees'           => 38500.00,
                'is_active'              => true,
                'overview'               => '<p>The Doctor of Business Administration (DBA) is the premier practitioner doctorate for senior executives and experienced managers who want to conduct applied business research and develop evidence-based solutions to complex organizational challenges.</p><p>The hybrid format combines intensive weekend residencies (twice per year) with online coursework, allowing you to remain in your executive role throughout the program.</p>',
                'terms'                  => ['Fall'],
                'areas'                  => ['Business', 'Business Law & Leadership'],
                'requirements'           => [
                    'Master\'s degree (MBA or related field) from an accredited institution',
                    'Minimum 7 years of managerial or leadership experience',
                    'Current résumé or executive biography',
                    'Statement of research interest (1,000 words)',
                    'Three letters of recommendation from senior leaders or academics',
                    'Writing sample demonstrating analytical capability',
                ],
                'concentrations'         => [
                    ['name' => 'Strategic Management', 'description' => 'Competitive dynamics, corporate governance, and organizational transformation.'],
                    ['name' => 'Organizational Behavior & Leadership', 'description' => 'Leadership theory, change management, and organizational culture research.'],
                ],
                'courses'                => [
                    ['title' => 'Advanced Research Methods', 'description' => 'Quantitative and qualitative methodologies for applied business research.'],
                    ['title' => 'Strategic Leadership Theory', 'description' => 'Theoretical frameworks for executive leadership and organizational strategy.'],
                    ['title' => 'Doctoral Seminar in Management', 'description' => 'Literature review, research design, and academic writing at the doctoral level.'],
                ],
                'deadlines'              => ['Fall' => '2026-02-15'],
                'testimonials'           => [
                    [
                        'student_name'    => 'Robert Chang',
                        'graduation_year' => '2023',
                        'program_taken'   => 'Doctor of Business Administration',
                        'testimonial'     => 'The DBA gave me an entirely new framework for understanding my organization. I applied findings from my dissertation directly to a restructuring initiative that saved $4M in operating costs.',
                    ],
                ],
                'faculty'                => [
                    [
                        'name'           => 'Dr. Charles Hensley',
                        'department'     => 'Management & Strategy',
                        'courses_taught' => 'Strategic Leadership, Doctoral Research Methods',
                        'description'    => 'Dr. Hensley has advised Fortune 100 boards and published extensively on CEO succession planning and board governance.',
                    ],
                ],
            ],

            // 8 ─ Certificate in Cybersecurity
            [
                'title'                  => 'Graduate Certificate in Cybersecurity',
                'program_name'           => 'Graduate Certificate in Cybersecurity',
                'slug'                   => 'graduate-certificate-cybersecurity',
                'program_type'           => 'Certificate',
                'degree_coursework_name' => 'Graduate Certificate',
                'program_major'          => 'Cybersecurity',
                'college'                => 'College of Engineering',
                'program_format'         => 'asynchronous',
                'duration'               => '12 months',
                'credit_hours'           => 15,
                'program_fees'           => 6200.00,
                'is_active'              => true,
                'overview'               => '<p>The Graduate Certificate in Cybersecurity is a focused, 15-credit-hour program designed for IT professionals and software engineers who need to rapidly upskill in security. The fully online curriculum covers threat analysis, network defense, cryptography, and incident response.</p><p>Credits from this certificate may be applied toward our MS in Computer Science program.</p>',
                'terms'                  => ['Fall', 'Spring', 'Summer'],
                'areas'                  => ['Computer Science', 'Computer Science & Data Technology', 'Engineering'],
                'requirements'           => [
                    'Bachelor\'s degree in computer science, information technology, or a closely related field',
                    'Minimum GPA of 2.75',
                    'Familiarity with networking fundamentals (CompTIA Network+ or equivalent experience)',
                ],
                'concentrations'         => [],
                'courses'                => [
                    ['title' => 'Network Security & Defense', 'description' => 'Firewalls, IDS/IPS, VPNs, and securing enterprise network infrastructure.'],
                    ['title' => 'Ethical Hacking & Penetration Testing', 'description' => 'Authorized vulnerability assessment techniques used by security professionals.'],
                    ['title' => 'Cryptography & PKI', 'description' => 'Symmetric and asymmetric encryption, digital signatures, and certificate management.'],
                ],
                'deadlines'              => ['Fall' => '2026-07-01', 'Spring' => '2025-11-15', 'Summer' => '2026-03-15'],
                'testimonials'           => [
                    [
                        'student_name'    => 'Raj Patel',
                        'graduation_year' => '2024',
                        'program_taken'   => 'Graduate Certificate in Cybersecurity',
                        'testimonial'     => 'I finished the certificate in 9 months while working full-time and immediately passed my CISSP exam. The penetration testing course alone was worth the entire investment.',
                    ],
                ],
                'faculty'                => [
                    [
                        'name'           => 'Dr. Natasha Rivera',
                        'department'     => 'Electrical Engineering & Computer Science',
                        'courses_taught' => 'Network Security, Ethical Hacking',
                        'description'    => 'Former NSA security researcher and author of "Applied Threat Intelligence." Dr. Rivera holds CISSP, CEH, and OSCP certifications.',
                    ],
                ],
            ],

            // 9 ─ MS Health Informatics
            [
                'title'                  => 'MS in Health Informatics',
                'program_name'           => 'Master of Science in Health Informatics',
                'slug'                   => 'ms-health-informatics',
                'program_type'           => "Master's",
                'degree_coursework_name' => 'Master of Science',
                'program_major'          => 'Health Informatics',
                'college'                => 'College of Health Sciences',
                'program_format'         => 'asynchronous',
                'duration'               => '2 years',
                'credit_hours'           => 36,
                'program_fees'           => 16000.00,
                'is_active'              => true,
                'overview'               => '<p>The MS in Health Informatics sits at the intersection of healthcare, data science, and information technology. Graduates are uniquely positioned to lead digital transformation initiatives at hospitals, health systems, insurers, and health-tech companies.</p><p>The curriculum is developed in collaboration with regional health systems and prepares students for roles such as Chief Medical Information Officer, Health IT Project Manager, and Clinical Data Analyst.</p>',
                'terms'                  => ['Fall', 'Spring'],
                'areas'                  => ['Health Sciences', 'Computer Science & Data Technology'],
                'requirements'           => [
                    'Bachelor\'s degree in health sciences, nursing, computer science, or a related field',
                    'Minimum GPA of 3.0',
                    'Statement of purpose describing career goals in health informatics',
                    'Two letters of recommendation',
                    'Current résumé highlighting relevant experience',
                ],
                'concentrations'         => [
                    ['name' => 'Clinical Informatics', 'description' => 'EHR optimization, clinical decision support, and workflow analysis for care settings.'],
                    ['name' => 'Health Data Analytics', 'description' => 'Population health analytics, claims data, and outcomes research using large datasets.'],
                ],
                'courses'                => [
                    ['title' => 'Healthcare Information Systems', 'description' => 'EHR systems, HL7/FHIR standards, interoperability, and health IT governance.'],
                    ['title' => 'Clinical Data Management', 'description' => 'Data quality, data governance frameworks, and managing clinical research data.'],
                    ['title' => 'Population Health Analytics', 'description' => 'Using claims and EHR data to identify at-risk populations and measure outcomes.'],
                ],
                'deadlines'              => ['Fall' => '2026-04-15', 'Spring' => '2025-10-01'],
                'testimonials'           => [
                    [
                        'student_name'    => 'Danielle Owens',
                        'graduation_year' => '2023',
                        'program_taken'   => 'MS in Health Informatics',
                        'testimonial'     => 'Coming from a nursing background, this degree gave me the technical credibility to move into a Director of Clinical Informatics role at a large health system. The faculty understood both worlds.',
                    ],
                ],
                'faculty'                => [
                    [
                        'name'           => 'Dr. Michael Song',
                        'department'     => 'Health Sciences & Informatics',
                        'courses_taught' => 'Healthcare Information Systems, EHR Implementation',
                        'description'    => 'Dr. Song led the EHR implementation for a 12-hospital system before joining academia. He holds a PhD in Biomedical Informatics and an RHIA credential.',
                    ],
                ],
            ],

            // 10 ─ MS Public Administration (MPA)
            [
                'title'                  => 'Master of Public Administration',
                'program_name'           => 'Master of Public Administration',
                'slug'                   => 'master-public-administration',
                'program_type'           => "Master's",
                'degree_coursework_name' => 'Master of Public Administration',
                'program_major'          => 'Public Administration',
                'college'                => 'College of Arts & Sciences',
                'program_format'         => 'mixed',
                'duration'               => '2 years',
                'credit_hours'           => 42,
                'program_fees'           => 13200.00,
                'is_active'              => false,
                'overview'               => '<p>The Master of Public Administration (MPA) prepares future leaders for careers in government, nonprofit organizations, and public-private partnerships. The program emphasizes public policy analysis, budgeting and financial management, organizational leadership, and democratic governance.</p><p>The mixed-format program offers both online and on-campus options, with one required weekend residency per semester for in-person capstone workshops and networking.</p>',
                'terms'                  => ['Fall', 'Spring'],
                'areas'                  => ['Public Administration', 'Business Law & Leadership', 'Social Work'],
                'requirements'           => [
                    'Bachelor\'s degree from an accredited institution',
                    'Minimum GPA of 3.0',
                    'Statement of purpose (750 words) describing public service goals',
                    'Two letters of recommendation (academic or professional)',
                    'Writing sample demonstrating analytical ability',
                ],
                'concentrations'         => [
                    ['name' => 'Nonprofit Management', 'description' => 'Governance, fundraising strategy, grant writing, and volunteer management for nonprofit organizations.'],
                    ['name' => 'Urban & Regional Policy', 'description' => 'Land use, housing policy, transportation planning, and economic development.'],
                ],
                'courses'                => [
                    ['title' => 'Public Policy Analysis', 'description' => 'Frameworks for evaluating policy options, stakeholder analysis, and policy advocacy.'],
                    ['title' => 'Public Budgeting & Finance', 'description' => 'Government budget cycles, fiscal management, performance budgeting, and financial reporting.'],
                    ['title' => 'Administrative Law & Ethics', 'description' => 'Legal foundations of public administration, regulatory process, and public sector ethics.'],
                ],
                'deadlines'              => ['Fall' => '2026-05-15', 'Spring' => '2025-10-15'],
                'testimonials'           => [
                    [
                        'student_name'    => 'Tanya Brooks',
                        'graduation_year' => '2022',
                        'program_taken'   => 'Master of Public Administration',
                        'testimonial'     => 'The MPA helped me make the leap from a program coordinator role to managing a $2M federal grant portfolio. The budgeting and policy courses gave me skills I use every single week.',
                    ],
                ],
                'faculty'                => [
                    [
                        'name'           => 'Dr. Samuel Pierce',
                        'department'     => 'Political Science & Public Policy',
                        'courses_taught' => 'Public Policy Analysis, Administrative Law',
                        'description'    => 'Former state budget director and policy advisor. Dr. Pierce has testified before Congress on federal grant reform and consults with municipal governments on performance management.',
                    ],
                ],
            ],

        ];

        foreach ($programs as $data) {
            $program = Program::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'title'                  => $data['title'],
                    'program_name'           => $data['program_name'],
                    'featured_image'         => null,
                    'program_type_id'        => $types[$data['program_type']],
                    'degree_coursework_name' => $data['degree_coursework_name'],
                    'program_major'          => $data['program_major'],
                    'college_id'             => $colleges[$data['college']],
                    'program_format'         => $data['program_format'],
                    'duration'               => $data['duration'],
                    'credit_hours'           => $data['credit_hours'],
                    'program_fees'           => $data['program_fees'],
                    'overview'               => $data['overview'],
                    'is_active'              => $data['is_active'],
                ]
            );

            // Admission terms
            $termIds = collect($data['terms'])->map(fn ($t) => $terms[$t])->filter();
            $program->admissionTerms()->sync($termIds);

            // Areas of interest
            $areaIds = collect($data['areas'])->map(fn ($a) => $areas[$a] ?? null)->filter();
            $program->areasOfInterest()->sync($areaIds);

            // Requirements
            $program->requirements()->delete();
            foreach ($data['requirements'] as $i => $req) {
                ProgramRequirement::create([
                    'program_id'  => $program->id,
                    'requirement' => $req,
                    'sort_order'  => $i,
                ]);
            }

            // Concentrations
            $program->concentrations()->delete();
            foreach ($data['concentrations'] as $i => $conc) {
                Concentration::create([
                    'program_id'  => $program->id,
                    'name'        => $conc['name'],
                    'description' => $conc['description'],
                    'sort_order'  => $i,
                ]);
            }

            // Featured courses
            $program->featuredCourses()->delete();
            foreach ($data['courses'] as $i => $course) {
                FeaturedCourse::create([
                    'program_id'  => $program->id,
                    'title'       => $course['title'],
                    'description' => $course['description'],
                    'sort_order'  => $i,
                ]);
            }

            // Deadlines
            $program->deadlines()->delete();
            foreach ($data['deadlines'] as $termName => $date) {
                if (isset($terms[$termName])) {
                    ProgramDeadline::create([
                        'program_id'        => $program->id,
                        'admission_term_id' => $terms[$termName],
                        'deadline_date'     => $date,
                    ]);
                }
            }

            // Testimonials
            $program->testimonials()->delete();
            foreach ($data['testimonials'] as $i => $t) {
                Testimonial::create([
                    'program_id'      => $program->id,
                    'student_name'    => $t['student_name'],
                    'graduation_year' => $t['graduation_year'],
                    'program_taken'   => $t['program_taken'],
                    'testimonial'     => $t['testimonial'],
                    'sort_order'      => $i,
                ]);
            }

            // Faculty
            $program->faculty()->delete();
            foreach ($data['faculty'] as $i => $f) {
                Faculty::create([
                    'program_id'     => $program->id,
                    'name'           => $f['name'],
                    'department'     => $f['department'],
                    'courses_taught' => $f['courses_taught'],
                    'description'    => $f['description'],
                    'sort_order'     => $i,
                ]);
            }
        }
    }
}
