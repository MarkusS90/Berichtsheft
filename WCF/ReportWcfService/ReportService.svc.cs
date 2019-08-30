using System;
using System.Collections.Generic;
using System.Linq;
using System.Xml.Linq;
using LinqToDB;
using LinqToDB.Data;
using SpookyScaryProjects.WpfBoilerplate;

namespace ReportWcfService
{
    /// <summary>Accesses the reports DB.</summary>
    public class ReportService : DataConnection, IReportService
    {
        private ITable<User> _Users => GetTable<User>();
        private ITable<Report> _Reports => GetTable<Report>();
        private ITable<Instructs> _Instructs => GetTable<Instructs>();
        private ITable<Institution> _Institutions => GetTable<Institution>();

        /// <summary>Creates the service instance.</summary>
        public ReportService() : base("Reports") { }

        /// <summary>Returns the user's reports.</summary>
        public List<Report> GetReports(Int32 apprenticeId)
        {
            IEnumerable<Report> reports = _Reports.Where(r => r.ApprenticeId.Equals(apprenticeId)).AsEnumerable();
            return _SetDaysFromContent(reports).OrderBy(r => r.Begin).ToList();
        }

        /// <summary>Groups the apprentice's reports by their year.</summary>
        public List<List<Report>> GetReportsByYear(Int32 apprenticeId)
        {
            return _Reports
                .Where(r => r.ApprenticeId.Equals(apprenticeId))
                .GroupBy(r => r.Year)
                .Select(g => g.Select(gg => gg).ToList()).ToList();
        }

        /// <summary>Returns the user's reports which are verified.</summary>
        public List<Report> GetVerifiedReports(Int32 apprenticeId)
        {
            IEnumerable<Report> reports = _Reports.Where(r => r.ApprenticeId.Equals(apprenticeId) && r.VerifiedBy != null).AsEnumerable();
            return _SetDaysFromContent(reports).ToList();
        }

        /// <summary>Returns the user's reports which are not verified.</summary>
        public List<Report> GetNotVerifiedReports(Int32 apprenticeId)
        {
            IEnumerable<Report> reports = _Reports.Where(r => r.ApprenticeId.Equals(apprenticeId) && r.VerifiedBy == null).AsEnumerable();
            return _SetDaysFromContent(reports).ToList();
        }

        /// <summary>Verifies a report.</summary>
        public void VerifyReport(Int32 reportId, Int32 verifiedBy)
        {
            _Reports
                .Where(r => r.Id.Equals(reportId))
                .Set(r => r.VerifiedBy, verifiedBy)
                .Update();
        }

        /// <summary>Unverifies a report.</summary>
        public void UnverifyReport(Int32 reportId, Int32 verifiedBy)
        {
            _Reports
                .Where(r => r.Id.Equals(reportId))
                .Set(r => r.VerifiedBy, null as Int32?)
                .Update();
        }

        /// <summary>Inserts a report into the DB.</summary>
        public void InsertReport(Report report)
        {
            _Reports.InsertWithInt32Identity(() => new Report()
            {
                ApprenticeId = report.ApprenticeId,
                Begin = report.Begin.Equals(DateTime.MinValue) ? DateTime.Now : report.Begin,
                End = _GetEndDate(report),
                Content = _GetXmlString(report),
                Comment = report.Comment,
                VerifiedBy = null,
                Year = report.Year.Min(1)
            });
        }

        /// <summary>Updates a report.</summary>
        public void UpdateReport(Report report)
        {
            _Reports.InsertOrUpdate(() => new Report()
            {
                Id = report.Id,
                ApprenticeId = report.ApprenticeId,
                Begin = report.Begin,
                End = _GetEndDate(report),
                Content = _GetXmlString(report),
                Comment = report.Comment,
                VerifiedBy = report.VerifiedBy,
                Year = report.Year.Min(1)
            },
            r => new Report()
            {
                Id = report.Id,
                ApprenticeId = report.ApprenticeId,
                Begin = report.Begin,
                End = _GetEndDate(report),
                Content = _GetXmlString(report),
                Comment = report.Comment,
                VerifiedBy = report.VerifiedBy,
                Year = report.Year.Min(1)
            }); 
        }

        /// <summary>Removes a report from the DB.</summary>
        public void DeleteReport(Int32 id)
        {
            _Reports.Delete(r => r.Id.Equals(id));
        }

        /// <summary>Inserts a user into the DB.</summary>
        public void InsertUser(User user)
        {
            _Users.InsertWithInt32Identity(() => new User()
            {
                Name = user.Name,
                Password = user.Password,
                Type = user.Type,
                IsActive = user.IsActive
            });
        }

        /// <summary>Updates an existing user.</summary>
        public void UpdateUser(User user)
        {
            _Users.Update(u => new User()
            {
                Id = user.Id,
                Name = user.Name,
                Password = user.Password,
                Type = user.Type,
                IsActive = user.IsActive
            });
        }

        /// <summary>Sets the user's active property to false.</summary>
        public void DeleteUser(Int32 id)
        {
            _Users.Where(u => u.Id.Equals(id)).Set(u => u.IsActive, false);
        }

        /// <summary>Returns a list of all users where the user's type equals Instructor.</summary>
        public List<User> GetInstructors()
        {
            return _Users.Where(u => u.Type.Equals(UserTypes.Instructor)).ToList();
        }

        /// <summary>Returns a list of all users where the user's type equals Apprentice.</summary>
        public List<User> GetApprentices()
        {
            return _Users.Where(u => u.Type.Equals(UserTypes.Apprentice)).ToList();
        }

        /// <summary>Returns a list of all users where the user's type equals Ihk</summary>
        public List<User> GetInspectors()
        {
            return _Users.Where(u => u.Type.Equals(UserTypes.Ihk)).ToList();
        }

        /// <summary>Returns a list of all users where the user's name contains the provided string. This is case insensitive.</summary>
        public List<User> FindApprentice(String userName)
        {
            return _Users.Where(u => u.Name.ToUpper().Contains(userName.ToUpper()) && u.Type.Equals(UserTypes.Apprentice)).ToList();
        }

        /// <summary>Gets all apprentices of the specified instructor.</summary>
        public List<User> GetApprenticesOfInstructor(Int32 instructorId)
        {
            List<Instructs> instructs = _Instructs.Where(i => i.InstructorId.Equals(instructorId)).ToList();
            return instructs.Select(i => _Users.First(u => u.Id.Equals(i.ApprenticeId))).ToList();
        }

        /// <summary>Gets all instructors of the specified apprentice.</summary>
        public List<User> GetInstructorsOfApprentice(Int32 apprenticeId)
        {
            List<Instructs> instructs = _Instructs.Where(i => i.ApprenticeId.Equals(apprenticeId)).ToList();
            return instructs.Select(i => _Users.First(u => u.Id.Equals(i.InstructorId))).ToList();
        }

        /// <summary>Inserts a new instructor - apprentice relation into the DB.</summary>
        public void InsertInstruct(Instructs instruct)
        {
            _Instructs.InsertWithInt32Identity(() => new Instructs()
            {
                ApprenticeId = instruct.ApprenticeId,
                InstructorId = instruct.InstructorId
            });
        }

        /// <summary>Groups all activities of an apprentice by their caption and calculates their total duration.</summary>
        public List<Activity> GroupActivitiesByCaption(Int32 apprenticeId)
        {
            return GetReports(apprenticeId)
                .SelectMany(r => r.Days)
                .SelectMany(d => d.Activities)
                .GroupBy(r => r.Caption)
                .Select(g => new Activity()
                {
                    Caption = g.Key,
                    Duration = g.Sum(a => a.Duration)
                })
                .Where(a => !String.IsNullOrEmpty(a.Caption))
                .OrderByDescending(a => a.Duration)
                .ThenBy(a => a.Caption)
                .ToList();
        }

        /*
            Diese Methode des Dienstes gruppiert alle Aktivitäten eines Auszubildenden anhand der Bezeichnung der Aktivität.
            Zuerst werden alle Berichte des Auszubildenen abgerufen, woraufhin die Tage und Aktivitäten rekursiv in eine neue Sammlung
            projiziert werden. Anschließend werden die resultierenden Aktivitäten gruppiert und in eine neue Sammlung projiziert, in der die Gesamtdauer berechnet wurde.
            Daraufhin werden alle Aktivitäten herausgefiltert, deren Bezeichnung leer oder null ist, und sie werden erst nach der Dauer, dann nach der Bezeichnung sortiert und in eine Liste konvertiert.

            Durch Linq2Db wird diese sogenannte Ausdrucksbaumstruktur in SQL übersetzt und an den Server übermittelt. Die Antwort wird automatisch in Activity-Objekte konvertiert.
        */

        /// <summary>Returns the total duration of all of the report's activities.</summary>
        public Int32 GetTotalDurationOfActivities(Int32 reportId)
        {
            return _SetDaysFromContent(new[] { _Reports.FirstOrDefault(r => r.Id.Equals(reportId)) }).First()
                .Days.SelectMany(r => r.Activities)
                .Sum(a => a.Duration);
        }

        /// <summary>Returns a list of all institutions.</summary>
        public List<Institution> GetInstitutions()
        {
            return _Institutions.ToList();
        }

        /// <summary>Returns the user's institution.</summary>
        public Institution GetInstitutionOfUser(Int32 userId)
        {
            User user = _Users.First(u => u.Id.Equals(userId));
            return _Institutions.First(i => i.Id.Equals(user.InstitutionId));
        }

        private DateTime _GetEndDate(Report report)
        {
            return report.Begin.AddDays(4);
        }

        private IEnumerable<Report> _SetDaysFromContent(IEnumerable<Report> reports)
        {
            return reports.Select(r => r.With(rr => rr.Days = _GetDays(r.Content)));
        }

        #region Xml

        private String _GetXmlString(Report report)
        {
            return new XDocument(
                new XElement(nameof(Report), report.Days?.Select(d =>
                    new XElement(nameof(Day),
                        new XAttribute(nameof(DayOfWeek), d.DayOfWeek), d.Activities.Select(a =>
                            new XElement(nameof(Activity),
                                new XAttribute(nameof(Activity.Caption), a.Caption),
                                new XAttribute(nameof(Activity.Duration), a.Duration))))))).ToString();
        }

        private List<Day> _GetDays(String xml)
        {
            return XDocument.Parse(xml).Descendants(nameof(Day)).Select(d => new Day()
            {
                Activities = d.Elements(nameof(Activity)).Select(a => new Activity()
                {
                    Caption = a.Attribute(nameof(Activity.Caption)).Value,
                    Duration = Int32.Parse(a.Attribute(nameof(Activity.Duration)).Value)
                }).ToList(),
                DayOfWeek = (DaysOfWeek)Enum.Parse(typeof(DaysOfWeek), d.Attribute(nameof(DayOfWeek)).Value)
            }).ToList();
        }

        #endregion
    }
}