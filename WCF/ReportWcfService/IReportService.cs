using System;
using System.Collections.Generic;
using System.ServiceModel;
using System.ServiceModel.Web;

namespace ReportWcfService
{
    [ServiceContract]
    public interface IReportService
    {
        [OperationContract]
        List<Report> GetReports(Int32 apprenticeId);

        [OperationContract]
        List<List<Report>> GetReportsByYear(Int32 apprenticeId);

        [OperationContract]
        List<Report> GetVerifiedReports(Int32 apprenticeId);

        [OperationContract]
        List<Report> GetNotVerifiedReports(Int32 apprenticeId);

        [OperationContract]
        void UpdateReport(Report report);

        [OperationContract]
        void InsertReport(Report report);

        [OperationContract]
        void VerifyReport(Int32 reportId, Int32 verifiedBy);

        [OperationContract]
        void UnverifyReport(Int32 reportId, Int32 verifiedBy);

        [OperationContract]
        void DeleteReport(Int32 id);

        [OperationContract]
        void InsertUser(User user);

        [OperationContract]
        void UpdateUser(User user);

        [OperationContract]
        void DeleteUser(Int32 id);

        [OperationContract]
        List<User> GetInstructors();

        [OperationContract]
        List<User> GetApprentices();

        [OperationContract]
        List<User> GetInspectors();

        [OperationContract]
        List<User> FindApprentice(String userName);

        [OperationContract]
        List<User> GetApprenticesOfInstructor(Int32 instructorId);

        [OperationContract]
        List<User> GetInstructorsOfApprentice(Int32 apprenticeId);

        [OperationContract]
        List<Activity> GroupActivitiesByCaption(Int32 apprenticeId);

        [OperationContract]
        Int32 GetTotalDurationOfActivities(Int32 reportId);

        [OperationContract]
        List<Institution> GetInstitutions();

        [OperationContract]
        Institution GetInstitutionOfUser(Int32 userId);
    }
}